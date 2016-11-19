<?php
namespace Home\Controller;
use Core\Controller;
/**
 * 用作队列分发服务的
 * Update time：2016-5-21 09:35:40
 */
class QueueController extends Controller {

	protected function _initialize() {
		if (!IS_CLI) exit;
		define('UID', is_login());
	}

	// 统一调度（非阻塞性的）
	function run()
	{
		// 阻塞性的，会阻塞下面语句执行
		// file_put_contents('/data/wwwroot/test/run', date('Y-m-d H:i:s'));	

		// 转换为非阻塞性的
		swoole_timer_after(1, function(){
			file_put_contents('/data/wwwroot/test/run', date('Y-m-d H:i:s'));
		});

		swoole_timer_after(1, array($this, 'final_shop'));	// 异步非阻塞性的

		swoole_timer_after(1, array($this, 'order_auto_print'));	// 异步非阻塞性的

		swoole_timer_after(1, array($this, 'ParseIp'));	// 异步非阻塞性的


		// (NOW_TIME % 3 == 0) && 最多间隔3秒

		// (NOW_TIME % 5 == 0) && 最多间隔5秒
	}

	// 订单自动分单（一分钟之内没有被人工分单的订单会被自动分单）（在线支付订单在没有收到支付通知前不能操作）
	public static function final_shop()
	{	file_put_contents('/data/wwwroot/test/final_shop', date('Y-m-d H:i:s'));
		$order_cancel_time =60;
		$time = NOW_TIME - $order_cancel_time;

		$order_model = M('order');
		$order_list  = $order_model
		->where(array(array(array('pay_type' => 2, 'pay_status' => 1), array('pay_type' => 1), '_logic' => 'or')))
		->where(array('status' => 0, 'final_shop_id' => 0, 'create_time' => array('elt', $time)))
		->select();

		if($order_list) {
			$order_model->startTrans();
			foreach($order_list as $key => $val) {
				$order_id = $val['id'];

				// 更新订单状态
				if (!$order_model
					->where(array('id' => $order_id, 'version' => $val['version']))	// 用乐观锁处理并发
					->save(array(
						'final_shop_id' => $val['shop_id'],
						'update_time' => NOW_TIME,
						'version' => array('exp', 'version+1')
					))) {
					continue;
				}

				//生成订单日志
				$data = array(
					'order_id' 		=> $order_id,
					'uid'     		=> 0,
					'mid'     		=> 0,
					'note'     		=> '您的订单被系统自动分单！',
					'create_time'   => NOW_TIME,	// 模拟实时
				);
				M('order_log')->add($data);
			}
			if ($order_model->getDbError()) {
	            $order_model->rollback();
	            $order_model->error = '系统错误';
	            return false;
	        }
	        $order_model->commit();
	        // 发送通知短信（短信还是要写到队列里面去发，不然会导致这个地方很慢很慢很慢……）
		}
	}
	

	// 订单自动推送打印（自营须有final_shop_id，非自营不做限制）（受打印模式影响）
	function order_auto_print()
	{
		file_put_contents('/data/wwwroot/test/order_auto_print', date('Y-m-d H:i:s'));
		$order_model = M('order');
		$order_list  = $order_model
		->where(array(array(array('pay_type' => 2, 'pay_status' => 1), array('pay_type' => 1), '_logic' => 'or')))
		->where(array('print_status' => 0, 'final_shop_id' => array('neq', 0)))
		->select();

		if($order_list) {
			$order_model->startTrans();
			foreach($order_list as $key => $val) {
				$order_id = $val['id'];
				// 自动打印需要上锁，以防止打印多张
				if (0 == $order_model->lock(true)->getFieldById($order_id, 'print_status')) {
					D('S/order')->send_print_order($order_id);
				}
			}
			if ($order_model->getDbError()) {
	            $order_model->rollback();
	            $order_model->error = '系统错误';
	            return false;
	        }
	        $order_model->commit();
	        // 发送通知短信（短信还是要写到队列里面去发，不然会导致这个地方很慢很慢很慢……）
		}
	}

	// 自动关闭超时订单
	function chaoshi()
	{
		tag('Common/OrderStstic');
	}

	// 多少分钟之后自动提醒用户尽快支付
	function a()
	{
		$order_cancel_time = C('ORDER_TIMEOUT') ? : (8 * 60);
		$time = NOW_TIME - $order_cancel_time;
		$order_model = M('order');
		$order_list  = $order_model
		->where(array('status' => 0, 'pay_type' => 2, 'pay_status' => 0))
		->where(array('create_time' => array('elt', $time)))
		->select();

		if($order_list) {
			foreach($order_list as $key => $val) {
				$order_id = $val['id'];

		        // 发送通知短信（短信还是要写到队列里面去发，不然会导致这个地方很慢很慢很慢……）
			}
		}
	}


	// 多少分钟后未被处理的订单将被系统自动关闭


	// 解析出每个订单的IP信息，因为IP是不稳定的，如果不及时解析出来可能导致分析不准确
	function ParseIp()
	{
		// $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=';
		$url = 'http://ip.taobao.com/service/getIpInfo.php?ip=';
		$order_model = M('order');
		$order_list  = $order_model->where(array('create_ip_info' => ''))->select();
		if($order_list) {
			foreach($order_list as $key => $val) {
				$order_id = $val['id'];
				$ip = $val['create_ip'];
				$create_ip_info = file_get_contents($url . long2ip($ip));
				file_put_contents('/data/wwwroot/test/ParseIp', $url . long2ip($ip));
				// 更新
				if (!$order_model
					->where(array('id' => $order_id, 'create_ip_info' => ''))	// 也相当于用乐观锁处理并发，但正确的做法是保证进程不能交叉处理
					->save(array(
						'create_ip_info' => $create_ip_info,
					))) {
					continue;
				}
			}
		}
	}


	// 每隔一分钟自动检测所有打印机是否在线（这个可以用定时任务做）（定时任务还是用一个统一的文件做统一调度，避免频繁修改系统文件）

	// 每天固定时间段进行所有打印机自检

	// 每天固定时间段进行财务安全检测

	// 每天固定时间段进行财务报表统计

	// 每天固定时间段进行财务结算（一般在午夜和凌晨比较合适）

	// 每天固定时间段进行业务数据整理，清洗，过滤，挖掘

	// 每天固定时间段进行日志数据汇总整理，分析

	// 每天固定时间段进行密集计算，如地图数据的生成

	// 每天定时关闭开启系统

	// 每天定时给运营人员报告信息

	// 实时监控系统，发送预警报告

	// 定期整理，清除，过滤，更新缓存信息
}
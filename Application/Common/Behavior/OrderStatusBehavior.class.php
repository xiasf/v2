<?php
/**
 * 订单状态拦截行为
 * Update time：2016-5-6 11:37:23
 */
namespace Common\Behavior;
class OrderStatusBehavior {
	public function run(&$params) {
		if (stripos(__ACTION__, 'order') || stripos(__ACTION__, 'cart'))
			$this->order_timeout_closure();
	}


	// 支付超时订单拦截
	public static function order_timeout_closure()
	{
		$order_cancel_time = C('ORDER_TIMEOUT') ? : (15 * 60);
		$time = NOW_TIME - $order_cancel_time;

		$order_model = M('order');
		$order_list  = $order_model
		->where(array('status' => 0, 'pay_type' => 2, 'pay_status' => 0))
		->where(array('create_time' => array('elt', $time)))
		->select();

		if($order_list) {
			foreach($order_list as $key => $val) {
				$order_id = $val['id'];

				// 更新订单状态
				if (!$order_model
					->where(array('id' => $order_id, 'version' => $val['version']))	// 用乐观锁处理并发
					->save(array(
						'status' => 10,
						'update_time' => $val['create_time'] + $order_cancel_time,
						'version' => array('exp', 'version+1')
					))) {
					$order_model->rollback();
	            	return false;
				}

				//生成订单日志
				$data = array(
					'order_id' 		=> $order_id,
					'uid'     		=> 0,
					'mid'     		=> 10,
					'note'     		=> '您的订单超时未支付被系统自动关闭！',
					'create_time'   => $val['create_time'] + $order_cancel_time,	// 模拟实时
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
}
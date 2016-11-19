<?php
/**
 * 打印机上报请求 管理控制器
 * Update time：2016-3-6 17:40:55
 */

namespace S\Controller;
use Core\Controller;

class PrinterRequestController extends Controller {

	protected $apiKey 					= '413c703ceb8559143857a183283c86929c207591';	// apiKey
	protected $partner 					= 4099;											// 用户id
	protected $username 				= 'xiak';										// 用户名

	// 功能分发
	public function index() {
		$cmd = I('post.cmd/s');
		$request = I('post.request/s');
		if ($cmd = 'userrequest') {
			switch ($request) {
		    	case "request1":
		    		$this->userrequest_again_print();
		    		break;
	    		case "request2":

	    			break;
		    }
		} elseif ($cmd = 'finish') {
			$this->print_state_finish();
		}
	}


	// 按钮上报，重打（再次打印）
	public function userrequest_again_print () {
		$params = array(
            'machine_code' => I('post.machine_code/d'),
            'time' => I('post.time/d'),
            'sign' => I('post.sign/s'),
	    );
	    // 签名校验
		if (!$this->_cmd_check_sign($params['time'], $params['sign'])) exit;

		if (!$printer_info = M('printer')->where(array('machine_code' => $params['machine_code']))->find()) exit();	// 非法（系统没有次此合法印机）

		// 获取打印机上一次成功打印的小票
		if ($paper = M('order_paper')->where(array('' => $printer_info['printer_id'], 'print_status' => 1))->order('id desc')->field('order_id')->find()) {
			A('Order')->send_print_order($paper['order_id'], $printer_info['printer_id']);
		} else {
			// 没有哦
			$this->_send_content($printer_info['printer_id'], '没有哦！');
		}
	}


	// 打印状态上报 state ：1 为打印完成，2 为异常）
	public function print_state_finish() {
		$params = array(
            'dataid' => I('post.dataid/d'),
            'machine_code' => I('post.machine_code/d'),
            'printtime' => I('post.printtime/d'),
            'time' => I('post.time/d'),
            'state' => I('post.state/d'),
            'sign' => I('post.sign/s'),
	    );
	    // 签名校验
		if (!$this->_cmd_check_sign($params['time'], $params['sign'])) exit;

		$order_paper = M('order_paper');
		$order_paper->startTrans();

		// 查询票据信息
		$paper_info = $order_paper->where(array('paper_id' => $params['dataid']))->find();

		$printer_status = $params['state'] == 1 ? 3 : 4;		// 订单打印状态

		$paper_printer_status = $params['state'] == 1 ? 2 : 1;	// 票据打印状态

		// 更新票据打印状态
		if ($order_paper->where(array('paper_id' => $params['dataid']))->save(array('printer_status' => $paper_printer_status, 'print_time' => $params['printtime']))) {
			$order_paper->rollback();
            // $this->error = $this->error ? : '系统错误';
            return false;
		}
		// 更新订单打印状态
		if (M('order')->save(array('id' => $paper_info['order_id'], 'printer_status' => $printer_status, 'print_time' => $params['printtime'], 'update_time' => NOW_TIME))) {
			$order_paper->rollback();
            // $this->error = $this->error ? : '系统错误';
            return false;
		}
		$is_ok = $params['state'] == 1 ? '成功！' : '失败！';
		//生成订单日志
		$data = array(
			'order_id' 		=> $order_id,
			'uid'     		=> 0,
			'mid'     		=> 10,
			'note'     		=> '您的订单打印' . $is_ok,
			'create_time'   => NOW_TIME,
		);
		M('order_log')->add($data);
		//生成票据日志
		$data = array(
			'order_id' 		=> $params['dataid'],
			'mid'     		=> 10,
			'note'     		=> '票据打印' . $is_ok,
			'create_time'   => NOW_TIME,
		);
		M('order_paper_log')->add($data);

        if ($order_paper->getDbError()) {
            $order_paper->rollback();
            // $order_paper->error = $order_paper->error ? : '系统错误';
            return false;
        }
		$order_paper->commit();
	}


	// 向打印机推送点什么信息
    private function _send_content($printer_id, $content) {
        $printer = D('Printer');
        $printer->send_check($printer_id, $content);
    }

	// 上报sign验证
	private function _cmd_check_sign($time, $sign) {
		return strtoupper(md5($this->apiKey . $time)) == $sign;
	}

}
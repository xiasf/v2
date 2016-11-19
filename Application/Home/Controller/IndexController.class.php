<?php
namespace Home\Controller;
/**
 * 首页index控制器
 * Update time：2015-12-3 22:18:28
 */
class IndexController extends HomeController {

	public function d() {
		ob_start();
		echo str_repeat('1',1000);
		echo '>>>1<br />';
		ob_flush();
		flush();
		sleep(1);
		echo '>>>2<br />';
		ob_flush();
		flush();
		sleep(2);
		echo '>>>3<br />';
		ob_flush();
		flush();
		sleep(3);
		echo '>>>4<br />';
		ob_flush();
		flush();
		sleep(4);
		echo '>>>5<br />';
		ob_flush();
		flush();
	}

	function haha() {
		echo "string";
	}

	public function x() {
		$member = M('member');
		$member->startTrans();
		$member->save(array('uid'=>1, 'sex' => array('exp', 'sex + 1')));
		sleep(5);
	}

	public function x_() {
		$member = M('member');
		// $member->startTrans();
		// $member->save(array('uid'=>1, 'sex' => array('exp', 'sex + 1')));
		$member->select(1);
		// $member->commit();
	}


	public function t() {
		$member = M('member');
		$member->startTrans();
		echo '>>><br />';
		ob_flush();
		flush();

		$r = $member->lock(false)->find(1);

		echo '>>>'.$r['sex'].'<br />';
		ob_flush();
		flush();

		sleep(2);
		$r = $member->lock(false)->find(1);

		$a = $member->where(array('sex'=>array('lt',$r['sex']+1)))->save(array('uid'=>1, 'sex' => array('exp', 'sex + 1')));
		// var_dump($a);
		$member->commit();
		echo '>>>'.$r['sex'].'<br />';
		ob_flush();
		flush();

		$r = $member->lock(false)->find(1);
		sleep(2);

		echo '>>>'.$r['sex'].'<br />';
		ob_flush();
		flush();

		print_r($r);
	}


	public function t2() {
		$member = M('member');
		$member->startTrans();

		echo '>>><br />';
		ob_flush();
		flush();

		$r = $member->lock(false)->find(1);
		sleep(0);

		echo '>>>'.$r['sex'].'<br />';
		ob_flush();
		flush();

		$member->save(array('uid'=>1, 'sex' => 5));
		$member->commit();
		sleep(0);

		$r = $member->lock(false)->find(1);

		echo '>>>'.$r['sex'].'<br />';
		ob_flush();
		flush();

		print_r($r);
	}


	function _initialize () {
	}


	// 首页
	public function index() {
		$this->display();
	}


	// 订单查询
	public function get_remindorder() {
		$mobile = I('get.mobile/s');
		$where['mobile'] = $mobile;
		$order_model = M('order');
		$order_item_model = M('order_item');
		$order_item = $order_model->where($where)->order('id desc')->page(I('get.page/d'), 10)->select();
		if (is_array($order_item)) {
			foreach ($order_item as $key => $value) {
				$order_item[$key]['item'] = $order_item_model->order('id desc')->where(array('order_id' => $value['id']))->select();
				$order_item[$key]['create_time'] = date('Y-m-d H:i:s', $order_item[$key]['create_time']);
			}
		}
		$order_num = $order_model->where($where)->count('id');
		$page = new \Core\Page($order_num, 10);
		if (DEVICE == 'wap') {
			$page->setConfig('prev','上一页');
			$page->setConfig('next','下一页');
		}
		$page_show = $page->show();
		$this->ajaxReturn($order_item);
		// $this->assign('order_item', $order_item);
		// $this->assign('phone', $phone);
		// $this->assign('order_num', $order_num);
		// $this->assign('page_show', $page_show);
	}
}
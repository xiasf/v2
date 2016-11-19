<?php
/**
 * 订单管理控制器
 * Update time：2016-5-7 17:28:59
 */

/*

1：订单除了有shop_id外还有个final_shop_id字段，允许再次分单，如果有final_shop_id则以final_shop_id为准

2：final_shop_id存在的订单不能分单，即一个订单只能执行一次分单

3：人工分单是 分单 确认 推送到打印机 三步一起连贯的

4：每一个原始新订单都有一个shop_id，分单人员有一分钟的时间缓冲时间来分单，否则订单将被系统自动分单

5：平台商店必须分单，第三方商店没有分单，直接新单打印，而平台商店必须分单后才能打印

6：以上分单功能其实是“确认打印”的前提，对于不支持“分单”功能的店铺那么就没有这个步骤了，要么是新单打印，确认打印，新单打印并确认

7：分单的本质是使运营拥有一种订单分配调度的能力

8：注意：在线支付的订单，在没有成功支付（未收到支付成功通知）前，不能进行任何状态更改，将保持原有状态 待确认，这是一种特殊的订单，不确定的订单，因为没有付款，所以也不能被打印出来。

9：每个店铺都有打印模式，自动打印受打印模式的影响，如确认打印模式下面订单只有先确认了才会被自动打印，手动打印则不受打印模式影响

*/

namespace Seller\Controller;
use Core\Page;
use Seller\Logic\OrderLogic;

class OrderController extends BaseController {

	// 给予LBS获取订单附近店铺
	public function get_lbs() {
		$location = I('get.location');
		$shop_type = I('get.shop_type');
		$OrderLogic = new OrderLogic;
		$return = $OrderLogic->get_lbs($location, $shop_type);
		$this->ajaxReturn($return);
	}

	// 添加订单备注
	public function add_order_note() {
		$order_note = D('OrderNote');
		$data = $order_note->add_note();
		if (false === $data) {
			$this->error($order_note->getError());
		} else {
			$this->success($data);
		}
	}

	// 订单详情
	public function order_detail() {
		$order_model = M('order o');
		$order_id = I('get.id/d', 0);
        $order_data = $order_model
    	->join('LEFT JOIN __SHOP__ s ON s.id = o.shop_id')
    	->field('o.*,s.shop_name,s.shop_type,s.shop_business_type')
    	->where(array('o.id' => $order_id))
    	->find();

        if (!$order_data) $this->error('订单不存在！');

        // 读出分单的店铺信息
        if ($order_data['final_shop_id']) {
    		$order_data['final_shop'] = M('shop')->field('shop_name,shop_type,shop_business_type')->find($order_data['final_shop_id']);
    	}

        // 附加订单条目
        $order_item = M('order_item');
    	$order_data['item'] = $order_item->where(array('order_id' => $order_id))->select();
    	foreach ($order_data['item'] as &$pro) {
			if ($pro['product_img'])
				$pro['product_img'] = get_thumb_src($pro['product_img'], 50, 50, 'jpg', 3, 100);
		}

        // 订单日志
        $this->order_log = M('order_log')->where(array('order_id' => $order_id))->order('id desc')->select();

        // 订单备注
        $this->order_note = M('order_note')->where(array('order_id' => $order_id))->order('id desc')->select();

        // 订单打印统计
        $order_data['print_statistical'] = array(0,0,0,0);

        // 订单打印记录
        $order_print_log = M('order_paper')->where(array('order_id' => $order_id))->order('create_time desc')->select();
        $order_paper_log = M('order_paper_log');
        $printer = M('printer');
        foreach ($order_print_log as $key => &$value) {	// 附加每个票据的打印日志
        	$value['log'] = $order_paper_log->where(array('paper_id' => $value['paper_id']))->order('id desc')->select();
        	$value['printname'] = $printer->getFieldById($value['printer_id'], 'printname');
        	$order_data['print_statistical'][3] += 1;			// 全部
        	if ($value['print_status'] == 0) {					// 待打印
        		$order_data['print_statistical'][1] += 1;
        	} elseif ($value['print_status'] == 1) {			// 打印失败
				$order_data['print_statistical'][2] += 1;
        	} elseif ($value['print_status'] == 2) {			// 打印成功
				$order_data['print_statistical'][0] += 1;
        	}
        }
        $this->order_print_log = $order_print_log;

        $this->order_data = $order_data;

        $this->display();
	}

	// 订单列表
	public function order_list() {
        $order = M('order o');

        // 构建复杂的查询where

        $order_list= $order
        // ->join('LEFT JOIN __ORDER_ITEM__ oi ON oi.order_id = o.id')
        ->join('LEFT JOIN __SHOP__ s ON s.id = o.shop_id')
        ->field('o.*,s.shop_name,s.shop_type,s.shop_business_type,s.lng as shop_lng,s.lat as shop_lat,s.address as shop_address,s.address_reference as shop_address_reference')
        ->page(I('get.page/d'), 10)
        ->order('o.id desc')
        ->select();

        $shop = M('shop');
        $order_item = M('order_item');
        foreach ($order_list as $key => &$value) {
        	if ($value['final_shop_id']) {
        		$value['final_shop'] = $shop->field('id,shop_name,shop_type,shop_business_type,lng,lat,address,address_reference')->find($value['final_shop_id']);
        	}

        	$value['item'] = $order_item->where(array('order_id' => $value['id']))->select();

        	foreach ($value['item'] as &$pro) {
				if ($pro['product_img'])
					$pro['product_img'] = get_thumb_src($pro['product_img'], 50, 50, 'jpg', 3, 100);
			}

        }
        $this->order_list = $order_list;

        $this->order_count = $order->join('LEFT JOIN __SHOP__ s ON s.id = o.shop_id')->count('o.id');
        $page = new Page($this->order_count, 10);
        $this->page_show = $page->show();
		$this->display();
	}


	// （人工）分单 确认 推送到打印机（这可能比较耗时哦）
	public function final_shop_order() {
		$final_shop_id = I('post.final_shop_id/d', 0);
		$order_id = I('post.order_id/d', 0);
		$order = D('order');
		if ($order->sure_order($order_id) && $order->final_shop_order($final_shop_id, $order_id) && $order->send_print_order($order_id)) {
			$this->success('分单成功');
		} else {
			$this->error($order->getError());
		}
	}


	// 确认订单
	public function sure_order() {
		$order_id = I('post.order_id/d', 0);
		$order = D('order');
		if ($order->sure_order($order_id)) {
			$this->success('确认成功');
		} else {
			$this->error($order->getError());
		}
	}

	// 取消订单
	public function cancel_order() {
		$order_id = I('post.order_id/d', 0);
		$order = D('order');
		if ($order->cancel_order($order_id)) {
			$this->success('取消成功');
		} else {
			$this->error($order->getError());
		}
	}


	// 手动推送打印订单
	public function send_print_order($order_id) {
		$order_id = $order_id ? : I('post.order_id/d', 0);
		$order = D('order');
		if ($order->send_print_order($order_id)) {
			$this->success('推送成功');
		} else {
			$this->error($order->getError());
		}
	}


	// 获取订单信息
	public function get_order($id = '') {
		if (empty($id)) $id = I('get.id/d', 0);
		$this->ajaxReturn($this->_get_order($id));
	}


	// 获取订单信息
	private function _get_order($id) {
		return $this->find($id);
	}
}
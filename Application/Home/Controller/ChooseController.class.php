<?php
namespace Home\Controller;
/**
 * 点餐控制器
 * Update time：2016-4-25 01:17:24
 */
class ChooseController extends HomeController {

	public function index() {
		// if (!$this->lng = I('cookie.lng/s', '', 'strip_tags')) $this->redirect('Area/index');
		// if (!$this->lat = I('cookie.lat/s', '', 'strip_tags')) $this->redirect('Area/index');
		// if (!$this->address = I('cookie.address/s', '', 'strip_tags')) $this->redirect('Area/index');
		if (!$this->shop_id = I('get.shop_id/d')) $this->redirect('Area/check');
		if (!$this->shop_name = M('shop')->getFieldById($this->shop_id, 'shop_name')) $this->redirect('Area/check');
		cookie('shop_id', $this->shop_id);	// 商店ID必须，种下shop_id cookie
		$this->display();
	}


	// 获取云分类或者普通分类
	public function get_products_category($shop_id = '') {
		if ($shop_id == '') $shop_id = I('cookie.shop_id/d');
		$shop_info = M('shop')->field('shop_type,shop_business_type')->find($shop_id);

		if(1 == $shop_info['shop_business_type']) {
			// 云餐厅，获取某个站的云分类
			$category_list = M('CloudProductsCategory')->where(array('is_del' => 0, 'status' => 1, 'shop_type' => $shop_info['shop_type']))->order('sort asc, id desc')->select();
		} else {
			// 普通餐厅，获取商店自定义分类即可
			$category_list = M('shop_products_category')->where(array('is_del' => 0, 'status' => 1, 'shop_id' => $shop_id))->order('sort asc, id desc')->select();
		}
		return array($shop_info['shop_business_type'], $category_list);
	}


	// 获取产品信息
	// 根据商店ID获取当前购物车信息，自动判断是否为云餐厅
	public function get_products_json($shop_id = '') {
		$products_category = $this->get_products_category($shop_id);
		if (1 == $products_category[0]) {
			$k = '__CLOUD_PRODUCTS_CATEGORY_EXTEND__';
		} else {
			$k = '__SHOP_PRODUCTS_CATEGORY_EXTEND__';
		}
		$products = M('products p');
		// 遍历分类，扩展分类item为其餐品列表
		foreach ($products_category[1] as $key => &$value) {
			$value['item'] = $products->join('LEFT JOIN ' . $k . ' e ON e.products_id = p.id')->field('p.*')->where(array('p.is_del' => 0, 'p.status' => 1, 'e.category_id' => $value['id']))->order('p.sort asc, p.id desc')->select();
			foreach ($value['item'] as &$pro) {
				if ($pro['img'])
					$pro['img'] = get_thumb_src($pro['img'], 70, 70, 'jpg', 3, 100);
			}
		}
		$this->ajaxReturn($products_category[1]);
	}

	// 获取购物车合法信息
	// 根据商店ID获取当前购物车信息，自动判断是否为云餐厅
	public function get_cart_json($shop_id = '') {
		$cart = I('cookie.cart/s');
		$data = array(
			'num' => 0,
			'total' => 0,
			'list' => array(),
		);
		$_cart = '';
		if (!A('Home/Cart')->check_cart_cookie($cart)) {
			cookie('cart', null);
		} else {
			$item = explode('-', $cart);
			$products_category = $this->get_products_category($shop_id);
			$tem = array();
			foreach ($products_category[1] as $value) {
				$tem[] = $value['id'];
			}

			if (!$tem) {
				$this->ajaxReturn($data);
			}

			// 不同经营类别的商店处理不同
			if (1 == $products_category[0]) {
				$join = 'LEFT JOIN __CLOUD_PRODUCTS_CATEGORY_EXTEND__ e ON e.products_id = p.id';
				$where = array('e.category_id' => array('in', $tem));
			} else {
				$join = '';
				$where = array('p.shop_id' => $shop_id);
			}
			$products = M('products p');
			foreach ($item as $key => $value) {
				list($id, $num) = explode('.', $value);
				$where = array_merge($where, array('p.id' => $id, 'p.is_del' => 0, 'p.status' => 1, 'p.inventory' => array('gt', 0)));
				if ($products_info = $products->field('p.*')->join($join)->where($where)->find()) {
					$data['list'][$products_info['id']] = array(
						'name' => $products_info['name'],
						'price' => $products_info['price'],
						'num' => $num,
						'inventory' => $products_info['inventory'],
						'total' => $products_info['price'] * $num,
					);
					$data['num'] += $num;
					$data['total'] += $data['list'][$products_info['id']]['total'];

					// $_cart .= $id . '.' . $num . '-';
				}
			}
			$data['total'] = number_format($data['total'], 2);
			/*
			// 暂不干预
			$_cart = trim($_cart, '-');
			cookie('cart', $_cart);	// 重置购物车，已过滤掉非法信息
			*/
		}
		$this->ajaxReturn($data);
	}
}
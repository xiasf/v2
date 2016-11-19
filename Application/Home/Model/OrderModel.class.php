<?php
/**
 * 订单模型
 * Update time：2016-5-5 14:58:00
 */

namespace Home\Model;
use Core\Model;
class OrderModel extends Model {
    protected $insertFields = 'note,lng,lat,location,pay_type,address,mobile,user_name,user_sex,address_tag';
    // protected $updateFields = '';

    protected $_validate = array(
    	array('lng', 'require', '请选择地址 [1]', 1),
		array('lat', 'require', '请选择地址 [2]', 1),
		array('location', 'require', '请选择地址 [3]', 1),
		array('address', 'require', '请填写详细地址', 1),
		array('mobile', 'mobile', '手机号码格式错误', 1, 'regex'),
		array('pay_type', '1,2', '支付方式参数错误', 1, 'in'),
    );

    protected $_auto = array(
    	array('create_ip', 'get_client_ip', 1, 'function', 1),
		array('create_useragent', 'I', 1, 'function', 'server.HTTP_USER_AGENT'),
		array('create_terminal', 'get_terminal', 1, 'callback'),
		array('create_time', NOW_TIME, 1),
		array('uid', 'is_login', 1, 'function'),
    );


    // 获取下单设备
    protected function get_terminal() {
    	return DEVICE == 'pc' ? 1 : 2;
    }


    // 获取商家信息，如果是云平台则返回分类列表
    private function _get_shop_info($shop_id = '', $cart_products_list = null) {
    	// 自动匹配商店
    	if ($shop_id == 'cart' && is_array($cart_products_list)) {
    		foreach ($cart_products_list as $value) {
    			list($id, ) = explode('.', $value);
    			if ($shop_id = M('products')->getFieldById($id, 'shop_id')) {
    				continue;
    			}
    		}
    	}
		if ($shop_id == '') $shop_id = I('cookie.shop_id/d');
		$shop_info = M('shop')->field('id,shop_name,shop_type,shop_business_type')->find($shop_id);
		$result = array($shop_info);
		if(1 == $shop_info['shop_business_type']) {
			// 云餐厅，获取某个站的云分类
			$category_list = M('CloudProductsCategory')->where(array('is_del' => 0, 'status' => 1, 'shop_type' => $shop_info['shop_type']))->order('sort asc, id desc')->select();
			$result[] = $category_list;
		}
		return $result;
    }


    // 结算/结算检查
    public function checkout($cart, $shop_id) {
		$cart_products_list = explode('-', $cart);
		if (!is_array($cart_products_list) || empty($cart_products_list)) {
			$this->error = '餐品数量错误，参数非法！';
			return false;
		}

		$shop_info = $this->_get_shop_info($shop_id);

		if (!$shop_info && (!$shop_info = $this->_get_shop_info('cart', $cart_products_list))) {
			$this->error = '购物车非法！';
			return false;
		}

		$order_info = array(
			'shop_id' 		=> '',
			'order_name' 	=> '',
			'product_num' 	=> 0,
			'total' 		=> 0,
			'list' 			=> array(),
		);
		if (IS_POST) $this->startTrans();
		$products_model = M('products p');
		$_cart = '';

		$shop_id = $shop_info[0]['id'];
		$shop_type = $shop_info[0]['shop_type'];
		$shop_business_type = $shop_info[0]['shop_business_type'];

		// 订单名称 商店名称 - 类别
		$order_info['order_name'] = $shop_info[0]['shop_name'] . '(' . ($shop_business_type == 1 ? '平台' : '第三方') . ')' . ' - '. get_shop_type($shop_type);
		$order_info['shop_id'] = $shop_id;

		// if ($shop_business_type == 2) $order_info['final_shop_id'] = $shop_id;	// 第三方没有分单，下单时就确定好 final_shop_id

		// 不同经营类别的商店处理不同
		if (1 == $shop_business_type) {
			// $list = array();
			// foreach ($shop_info[1] as $value) {
			// 	$list[] = $value['id'];
			// }
			// $join = 'LEFT JOIN __CLOUD_PRODUCTS_CATEGORY_EXTEND__ e ON e.products_id = p.id';
			// $where = array('e.category_id' => array('in', $list));
			$join = 'LEFT JOIN __CLOUD_PRODUCTS_CATEGORY_EXTEND__ e ON e.products_id = p.id';
			$join .= ' LEFT JOIN __CLOUD_PRODUCTS_CATEGORY__ c ON c.id = e.category_id';
			$where = array('c.shop_type' => $shop_type);
		} else {
			$join = '';
			$where = array('p.shop_id' => $shop_id);
		}
		foreach ($cart_products_list as $key => $value) {
			list($id, $num) = explode('.', $value);
			$where = array_merge($where, array('p.is_del' => 0, 'p.status' => 1, 'p.id' => $id));
			if ($tem = $products_model->field('p.*')->join($join)->where($where)->lock(IS_POST)->find()) {
				$order_info['list'][$key] = array(
					'product_id' 		=> $id,
					'product_name' 		=> $tem['name'],
					'product_price' 	=> $tem['price'],
					'product_num' 		=> $num,
					'product_unit'		=> $tem['unit'],
					'product_img'		=> $tem['img'],
					'summary' 			=> $tem['price'] * $num,
				);
				if (IS_POST) {
					if (!$num) {
						$this->rollback();
            			$this->error = '餐品数量错误，参数非法！';
            			return false;
					}

					if ($tem['inventory'] < $num) {
						$this->rollback();
		            	$this->error = $tem['name'].' 剩余库存('.$tem['inventory'].') 不足'.$num.'件哦';
		            	return false;
					} else {
						if (1 !== $products_model->where(array('id' => $id))->setDec('inventory', $num)) {
							$this->rollback();
			            	$this->error = $this->error ? : '更新库存失败';
			            	return false;
						}
					}
				}
				$order_info['product_num'] += $num;
				$order_info['total'] += $order_info['list'][$key]['summary'];
				$order_info['total'] = number_format($order_info['total'], 2);
				$_cart .= $id . '.' . $num . '-';
			} else {
				continue;
			}
		}
		
		// 这样做的不科学，暂不处理（我们只保证取得的是“合法”信息就可以了）
		// $_cart = trim($_cart, '-');
		// cookie('cart', $_cart);		// 重置购物车，已过滤掉非法信息（并不过滤非法库存的，库存只在提交时检查）

		if (empty($order_info['list'])) {
			if (IS_POST) {
				$this->rollback();
            	$this->error = '餐品不存在，参数非法！';
            	return false;
			} else {
				return false;
			}
		}
		if (IS_POST) {
			$_auto = array(
				array('product_num', $order_info['product_num']),
				array('total', $order_info['total']),
				array('order_name', $order_info['order_name']),
				array('shop_id', $order_info['shop_id']),
			);
			$_auto = array_merge($this->_auto, $_auto);

			if (!$this->auto($_auto)->create() || (!$new_id = $this->add())) {
				$this->rollback();
            	$this->error = $this->error ? : '创建订单失败';
            	return false;
			}

			$log = array(
	            'order_id' => $new_id,
	            'uid' => is_login(),
	            'note' => '您的订单创建成功！',
	            'create_time' => NOW_TIME,
	            'mid' => 0,
	        );
	        M('order_log')->add($log);

			$order_item_model = M('order_item');
			foreach ($order_info['list'] as $key => &$value) {
				$value['order_id'] = $new_id;
			}

			// print_r($order_info['list']);

			if (!$order_item_model->addAll($order_info['list'])) {
				$this->error = $order_item_model->error ? : '创建订单条目失败';
				$this->rollback();
				return false;
			}

			$cart = trim(str_replace(trim($_cart, '-'), '', $cart), '-');
			cookie('cart', $cart ? : null);	// 删除下单成功的购物车部分

			cookie('cart_mobile', $_POST['mobile']);
			cookie('cart_address', I('post.address'));
			cookie('cart_user_name', I('post.user_name'));
			$this->commit();
            return $new_id;
		}
		return $order_info;
    }
}
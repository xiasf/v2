<?php
namespace Home\Controller;
/**
 * 购物车、结算控制器
 * Update time：2016-6-12 23:01:45
 */
class CartController extends HomeController {
	
	// 订单支付
	public function pay() {
		$order_id = I('sf_auto.order_id/d');
		$order_model = M('order');
		$order_model->startTrans();
		$order = $order_model->lock(true)->find($order_id);
		if ($order) {
			if (($order['status'] == 0 || $order['status'] == 10) && $order['pay_type'] == 2 && $order['pay_status'] == 0) {
			} else {
				if (IS_POST) {
					$result = array('status' => 0, 'info' => '订单状态错误(不满足支付条件)');
					$this->ajaxReturn($result);
				}
			}
			$this->end_time = $order['create_time'] + C('ORDER_TIMEOUT');
			$this->t  = $order['create_time'] + C('ORDER_TIMEOUT') - NOW_TIME;
			if ($order['status'] == 0 && $order['pay_type'] == 2 && $order['pay_status'] == 0 && $this->t < 0) {	// 超时关闭订单
				if ($order_model->where(array('id' => $order['id']))->save(array('status' => 10))) {
					$order_model->commit();

					//生成订单日志
					$data = array(
						'order_id' 		=> $order_id,
						'uid'     		=> 0,
						'mid'     		=> 10,
						'note'     		=> '订单超时未支付被系统自动关闭！',
						'create_time'   => $order['create_time'] + $this->end_time,	// 模拟实时
					);
					M('order_log')->add($data);

				} else {
					$order_model->rollback();
					// 失败可另外单独记录日志
					trace('超时关闭订单失败！', 'ERROR', true);
				}
				$order['status'] = 10;
				if (IS_POST) {
					$result = array('status' => 0, 'info' => '订单已超时，请重新下单');
					$this->ajaxReturn($result);
				}
			}
			$beecloud = array(
				'appSecret' 	=> C('BEECLOUD.appSecret'),
				'app_id' 		=> C('BEECLOUD.app_id'),
				'bill_no'		=> C('BEECLOUD.bill_no_prefix') . $order_id,
				'timestamp' 	=> NOW_TIME * 1000,
				'return_url' 	=> C('BEECLOUD.return_url'),
				'show_url' 		=> C('BEECLOUD.show_url'),
				'optional' 		=> json_decode(json_encode(array("tag"=>"msgtoreturn"))),
				'bill_timeout'	=> $this->end_time,
				'title'			=> $order['name'],
				'total_fee'		=> intval($order['total'] * 100),
			);
			$beecloud['app_sign'] = md5($beecloud["app_id"] . $beecloud["timestamp"] . $beecloud['appSecret']);
			require_cache(COMMON_PATH . 'Server/Pay/beecloud.class.php');

			// 微信支付
			if (is_wechat() && IS_GET) {
				require_cache(COMMON_PATH . 'Server/Pay/dependency/WxPayPubHelper/WxPayPubHelper.class.php');
				$jsApi = new \JsApi_pub();
				if (!isset($_GET['code']) || $_GET['code'] == session('wx_code')) {
					$baseUrl = urlencode('http://' . APP_DOMAIN . __SELF__);
					$url = $jsApi->createOauthUrlForCode($baseUrl);
				    Header("Location: $url");
				} else {
				    $code = $_GET['code'];
				    $jsApi->setCode($code);
				    $this->openid = $openid = $jsApi->getOpenId();
				    session('wx_code', $code);
				}
				$beecloud["channel"] = "WX_JSAPI";
				$beecloud["openid"] = $openid;
				try {
				    $result = \BCRESTApi::bill($beecloud);
				    if ($result->result_code != 0 && $result->result_code != 7) {
				        echo json_encode($result);
				        exit();
				    }
				    $this->jsApiParam = array(
				    	"appId" => $result->app_id,
				        "timeStamp" => $result->timestamp,
				        "nonceStr" => $result->nonce_str,
				        "package" => $result->package,
				        "signType" => $result->sign_type,
				        "paySign" => $result->pay_sign
				    );
				} catch (Exception $e) {
				    echo $e->getMessage();
				}
			}
			// 其它支付请求（返回第三方渠道付款链接）
			if (IS_POST) {
				$result = array('status' => 0, 'info' => '抱歉，服务忙，请稍后再试哦！');
				$beecloud["channel"] = I('post.channel');
				try {
				    $result = \BCRESTApi::bill($beecloud);
				    if ($result->result_code != 0) {
				        // echo json_encode($result);  // 非调试阶段可以关闭错误信息
				        // exit;
				    } else {
				    	$result = array('status' => 1, 'info' => $result->url);
				    }
				} catch (Exception $e) {
					// $result['info'] = $e->getMessage();
				}
				$this->ajaxReturn($result);
			}
			$this->assign('order', $order);
		} else {
			$this->error('订单不存在！');
		}
		$this->display('pay');
	}


	// BEECLOUD回调
	public function webhook() {
		$appSecret = C('BEECLOUD.appSecret');
		$appId = C('BEECLOUD.app_id');
		$jsonStr = file_get_contents("php://input");
		$msg = json_decode($jsonStr);
		$sign = md5($appId . $appSecret . $msg->timestamp);
		if ($sign != $msg->sign) {
		    exit;
		}
		$order_id = substr($msg->transaction_id, strlen(C('BEECLOUD.bill_no_prefix')));
		$order_model = M('order');
		$order_model->startTrans();
		$order = $order_model->lock(true)->find($order_id);	// 对订单加锁保证一致性

		$order_msg = json_decode($order['webhook_json']);

		if (!$order) {
        	// 订单被删除了？
        	trace('order_id:' . $order_id . '，支付成功了，但是订单却被告知不存在啊！', 'ERROR', true);
        	exit;
        }
        if ($order['pay_status'] == 1) {
        	// 可能是重复支付
        	if ($order_msg->channelType == $msg->channelType) {
        		// 这种情况为渠道重试
        		exit;
        	} else {
        		// 这种情况才是要给当前渠道请求退款
        		// 退款
        		// $post_data = "account=cf_wwzh&password=qq5752020&mobile={$s['phone']}&content=".rawurlencode("尊敬的{$mis_name}，检测到您重复支付，系统将为您受理退款，请耐心等待");
				// $mis_str = $this->Post($post_data);
				trace('order_id:' . $order_id . '，有人从不同渠道都支付成功了啊，可能要退款啊！', 'ERROR', true);
        		exit;
        	}
        }
        // 只有付款成功的订单才能进行操作，其它状态全部视为非法
        if ($order['status'] != 0) {
        	trace('order_id:' . $order_id . '，是谁在未收到支付通知前更改了订单状态！', 'ERROR', true);
    		exit;
        }

		if ($msg->transactionType == "PAY") {
		    $result = $msg->tradeSuccess;
		    switch ($msg->channelType) {
		    	case "WX":
		        case "ALI":
		        	if ($order_model->where(array('id' => $order_id))->save(array('pay_status' => 1, 'pay_time' => NOW_TIME, 'webhook_json' => $jsonStr))) {
						$order_model->commit();

						//生成订单日志
						$data = array(
							'order_id' 		=> $order_id,
							'uid'     		=> 0,
							'mid'     		=> 1,
							'note'     		=> '订单支付成功！渠道：' . $msg->channelType,
							'create_time'   => NOW_TIME,
						);
						M('order_log')->add($data);

		        	} else {
						$order_model->rollback();
						// 失败可另外单独记录日志
						trace('order_id:' . $order_id . '，订单支付状态更新失败！', 'ERROR', true);
		        	}
		        	// 发送短信
		        	$hsms_tpl_data = array(
						'{name}' => $order['user_name'],
						'{order_id}' => $order['id'],
					);
					hsms::send($order['mobile'], 'pay_ok', $hsms_tpl_data);

					$hsms_tpl_data = array(
						'{user_name}' => $order['user_name'],
						'{mobile}' => $order['mobile'],
						'{order_id}' => $order['id'],
						'{total}' => $order['total'],
						'{pay_type}' => $msg->channelType,
						'{time}' => date('Y-m-d H:i:s', NOW_TIME)
					);
					hsms::send(15997152146, 'pay_ok_xiak', $hsms_tpl_data);

		            break;
		        case "UN":
		            break;
		    }
		} else {
			exit;
		}
		file_put_contents(PATH . 'beecloud_webhook.php', print_r(json_decode($jsonStr, true), true), FILE_APPEND);
		echo 'success';
	}


	// 购物车
	public function checkout($shop_id = '') {
		$cart = I('cookie.cart/s');
		if (empty($cart)) {
			if (IS_POST)
				$this->ajaxReturn(array('status' => 0, 'info' => '没有购物车信息！'));
			else
				$this->redirect('no');
		}
		if (!$this->check_cart_cookie($cart)) {
			cookie('cart', null);
			if (IS_POST)
				$this->ajaxReturn(array('status' => 0, 'info' => '购物车信息非法！'));
			else
				$this->redirect('no');
		}
		$order_model = D('Order');
		$result = $order_model->checkout($cart, $shop_id);
		if (IS_POST) {
			if (false === $result) {
				$this->ajaxReturn(array('status' => 0, 'info' => $order_model->getError()));
			} else {
				$this->ajaxReturn(array('status' => 1, 'info' => $result));
			}
		} else {
			if (false === $result) {
				$this->error($order_model->getError());
			}
		}
		$this->assign('products_list', $result);
		$this->display();
	}


	public function no() {
		if ($_COOKIE['cart'])	// 防止被直接访问到，可以做只能跳转
			$this->redirect('checkout');
		$this->display();
	}


	// 中转
	public function index() {
		if ($_COOKIE['cart'])
			$this->redirect('checkout');
		else
			$this->redirect('no');
	}


	// 检测购物车
	public function check_cart_cookie($str) {
		return (boolean) preg_match('/^(\d+\.\d+-)*(\d+\.\d+)$/', $str);
	}
}
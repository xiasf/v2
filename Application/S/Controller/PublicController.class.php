<?php
/**
 * 后台模块控制器
 * Update time：2016-4-18 14:48:33
 */
namespace S\Controller;
use Core\Controller;
use Common\Server\User\Api\UserApi as UserApi;

class PublicController extends Controller {
	public function index() {
		if (is_login()) {
			$url = get_referer();
			redirect($url);
		} else {
			$this->redirect('Public/login');
		}
	}


	// 退出
	public function logout() {
		if (is_login()) {
			D('Common/Member')->logout();
			$this->success('退出成功！', U('Public/login'));
		} else {
			$this->redirect('Public/login');
		}
	}


	// 登陆
	public function login() {
		if (IS_POST) {
			$mobile 	= I('post.mobile/s');
			$password 	= I('post.password/s');
			$user = new UserApi;
			$uid = $user->login($mobile, $password);
			if (0 < $uid) {
				$Member = D('Common/Member');
				if ($Member->login($uid)) {
					// 登陆成功
					$this->success('登陆成功！', get_referer());
				} else {
					$this->error($Member->getError());
				}
			} else { //登录失败
				switch($uid) {
					case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
					case -2: $error = '密码错误！'; break;
					default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
				}
				$this->error($error);
			}
		} else {
			$this->redirectURL = get_referer();
			$this->display();
		}
	}
}
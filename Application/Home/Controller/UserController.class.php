<?php
namespace Home\Controller;
use Core\Controller;
use Common\Util\WechatOauth;
use Common\Model\OauthAccountModel as OauthAccount;
use Common\Server\User\Api\UserApi as UserApi;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 * Update time：2016-6-20 17:04:22
 */
class UserController extends Controller {

	public function index()
	{
		if (!is_login()) {
			$this->redirect('login');
		} else {
            $this->user_info = $this->get_user();
            $this->display();
        }
	}


	// 重定向到微信授权
    public function oauth()
    {
        $oauth_id = 1;
        // 五味真火微信公众号登录
        session('oauth_id', $oauth_id);

        $wechatOauth = new WechatOauth($oauth_id);
        $redirectUrl = urlencode(U('callback', '', true, true));
        // $redirectUrl = urlencode('http://hbdscy.com/v1');
        // echo $url = $wechatOauth->createOauthUrlForCode($redirectUrl, 'snsapi_userinfo');
        $wechatOauth->get_code($redirectUrl, 'snsapi_userinfo');
    }


    // 回调地址，作用：接收code并处理
    public function callback()
    {
        $oauth_id = session('oauth_id');
        $wechatOauth = new WechatOauth;
        // 回调验证
        $result = $wechatOauth->callback();
        if ($result) {
            // 非法回调
            if (isset($result['status']) && 0 == $result['status']) {
                $this->redirect('oauth');
            }
            if (isset($result['errcode'])) {   // 获取access_token失败
                E($result['errcode'] . ':' . $result['errmsg']);
            }
        } else {
            // echo '你不同意授权？';
            // 重定向到认证前的页面，登陆前的页面（这里也可以调到其他页面，一个挽留的页面）
            // $this->redirect(get_referer());
            $this->error('你不同意授权', get_referer());
        }

        $access_token = $result['access_token'];
        $openid = $result['openid'];
        $unionid = isset($result['unionid']) ? $result['unionid'] : null;

        // 获取用户信息（埋到页面）
        $this->oauth_user_info = $wechatOauth->getUserInfo($access_token, $openid);

        session('oauth', ['access_token' => $access_token, 'openid' => $openid, 'unionid' => $unionid, 'user_info' => $this->oauth_user_info]);

        $oauthAccount = new OauthAccount;


        // 好像只对于微信有这个
        // unionid是解决在一个开发者账号下的多个应用间统一用户帐号的问题的
        if ($unionid) {
            // 关联 unionid 与 openid
        }

        // 当前已登录
        if (is_login()) {

            // 检测当前账号是否绑定了第三方 
            if ($openid_ = $oauthAccount->getOpenidForUid($uid, $unionid, $oauth_id)) {

                // 获取第三方openid，并判断openid是否等于此openid
                if ($openid_ == $openid) {
                    // 你已绑定，不能重复绑定
                    // E('你已绑定，不能重复绑定');
                    $this->error('你已绑定，不能重复绑定', get_referer());
                } else {
                    // 已绑定其它第三方，只有先解绑才能绑定此openid
                    // E('你已绑定其它第三方，只有先解绑才能绑定此openid');
                    $this->error('你已绑定其它第三方，只有先解绑才能绑定此openid', get_referer());
                }

            } else {
                // 检测当前openid是否绑定了其它账号
                if ($oauthAccount->getUidForOpenid($openid, $unionid, $oauth_id)) {
                    // 当前openid已绑定其它账号，不能再绑定此账号
                    // E('当前openid已绑定其它账号，不能再绑定此账号');
                    $this->error('当前openid已绑定其它账号，不能再绑定此账号', get_referer());
                } else {
                    // 当前已经登录，且此openid还没被绑定，可以相互绑定
                    // 满足绑定要求（显示账号，和社交账号，一个按钮，是否绑定）
                    $this->redirect('bind_true');
                }
            }
        } else {    // 未登录

            // 检测当前openid是否绑定了账号，且是直接跳到授权页的
            // if (($uid = $oauthAccount->getUidForOpenid($openid, $unionid, $oauth_id)) && cookie('auto_oauth')) {
            if (($uid = $oauthAccount->getUidForOpenid($openid, $unionid, $oauth_id))) {
                // 方案2比较人性化

                // 方案1：
                // 直接自动登录（给个提示：检测到您已绑定过账号，已为你自动登录）
                // $this->autoOauthLoginAct($uid, $openid, $unionid, $oauth_id);

                // 方案2：
                // 显示当前微信账号信息，提示您的微信账号已绑定以上五味真火账号，是否直接登录：确认|使用其他五味真火账号登录
                $this->redirect('autoOauthLogin');

            } else {
                // 显示三个按钮

                // 1：绑定已有账号（并登陆）【关联已有账号】（关联后您的微信账号和**账号都可以登录）loginbind
                // 2：注册新账号并绑定（并登陆）【快速注册 并关联】（注册后您的微信账号和**账号都可以登录）regbind

                // 3：快速注册新账号并绑定（并登陆）（无登陆用户名和密码）（暂不需要）

                // 显示联合登录 页面
                $this->display('associated_account');
            }
        }
    }


    private function get_user() {
        return M('member')->find(session('user_auth.uid'));
    }


    private function is_re_callback() {
        $http_referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
        if (false !== stripos($url, 'callback'))
            return true;
        else
            return false;
    }


    // 立即绑定【登录绑定】
    public function bind_true()
    {
        if (IS_POST) {
            $oauth_id = session('oauth_id');
            $openid = session('oauth.openid');
            $unionid = session('oauth.unionid');
            $uid = is_login();
            if (!$uid) {
                $this->error('请登陆', U('User/login'));
            }
            $oauthAccount = new OauthAccount;
            $result = $oauthAccount->bindAccount($uid, $openid, $unionid, $oauth_id);
            if (1 != $result['status']) {
                $this->error($result['info']);
            } else {
                $this->success('绑定已有账号成功', get_referer());
            }
        } else {
            // if (!$this->is_re_callback()) {
            //     $this->redirect('oauth');
            // }
            $this->oauth_user_info = session('oauth.user_info');
            $this->userInfo = $this->get_user();
            $this->display();
        }
    }


    // 绑定已有账号（并登陆）【按钮一】【登录绑定】
    public function loginBind()
    {
        if (IS_POST) {
            $oauth_id = session('oauth_id');
            $openid = session('oauth.openid');
            $unionid = session('oauth.unionid');

            $login = $this->login_act();
            if (0 == $login['status']) {
                $this->error($login['info']);
            } else {
                $oauthAccount = new OauthAccount;
                $uid = $login['info'];
                $result = $oauthAccount->bindAccount($uid, $openid, $unionid, $oauth_id);
                if (1 != $result['status']) {
                    $this->error($result['info']);
                } else {
                    $this->success('绑定已有账号成功', get_referer());
                }
            }
        } else {
            // if (!$this->is_re_callback()) {
            //     $this->redirect('oauth');
            // }
        	$this->oauth_user_info = session('oauth.user_info');
            $this->display();
        }
    }


    // 注册新账号并绑定（并登陆）【按钮二】【注册绑定】
    public function regBind()
    {
        if (IS_POST) {
            $oauth_id = session('oauth_id');
            $openid = session('oauth.openid');
            $unionid = session('oauth.unionid');

            $reg = $this->register_act();
            if (0 == $reg['status']) {
                $this->error($reg['info']);
            } else {
                $oauthAccount = new OauthAccount;
                $uid = $reg['info'];
                $result = $oauthAccount->bindAccount($uid, $openid, $unionid, $oauth_id);
                if (1 != $result['status']) {
                    $this->error($result['info']);
                } else {
                    $this->success('注册新账号并绑定成功', get_referer());
                }
            }
        } else {
            // if (!$this->is_re_callback()) {
            //     $this->redirect('oauth');
            // }
        	$this->oauth_user_info = session('oauth.user_info');
            $this->display();
        }
    }



    // 自动oauth登录提示页面
    public function autoOauthLogin()
    {
        if (IS_POST) {
            $oauth_id = session('oauth_id');
            $openid = session('oauth.openid');
            $unionid = session('oauth.unionid');
            // 检测当前openid是否绑定了账号
            $oauthAccount = new OauthAccount;
            if ($uid = $oauthAccount->getUidForOpenid($openid, $unionid, $oauth_id)) {
                $result = $this->autoOauthLoginAct($uid, $openid, $unionid, $oauth_id);
            } else {
                $result = ['status' => 0, 'info' => '没有绑定不能自动登录！'];
            }
            $this->ajaxReturn($result);
        } else { //显示登陆页面
            // if (!$this->is_re_callback()) {
            //     $this->redirect('oauth');
            // }
        	$this->oauth_user_info = session('oauth.user_info');
            $this->display('auto_oauth_login');
        }
    }


    // 登录页面
    public function login()
    {
        if (IS_POST) {
            $result = $this->login_act();
            $this->ajaxReturn($result);
        } else { //显示登陆页面
            // 完成授权后的跳转
            cookie('redirectURL', get_referer(0));

            // if (is_wechat()) {
            //     cookie('auto_oauth', 1);
            //     $this->redirect('oauth');
            // }
            // cookie('auto_oauth', 0);
            $this->display();
        }
    }


    // 注册页面
    public function register()
    {
        if (IS_POST) {
            $result = $this->register_act();
            $this->ajaxReturn($result);
        } else { // 显示注册页面
            // 完成授权后的跳转
            cookie('redirectURL', get_referer(0));
            $this->display();
        }
    }


    // 发送短信验证码
    public function send_sms() {
    	if (IS_POST) {
    		$result = ['status' => 1, 'info' => ''];
			$business_code = 1001;
	        $mobile = I('post.mobile/s');
	        $sms = D('Common/SmsVerificationCode');
	        if ($sms->send_sms($mobile, $business_code)) {
	            $this->success('发送成功');
	        } else {
	            $this->error($sms->getError());
	        }
    	}
    }


    // 验证短信验证码
    public function check_sms($mobile, $smsCode, $business_code) {
    	if (IS_POST) {
    		$sms = D('Common/SmsVerificationCode');
	        if ($sms->check_sms($mobile, $smsCode, $business_code)) {
	            return 1;
	        } else {
	            return 0;
	        }
    	}
    }


    /* 执行登录操作 */
    private function login_act()
    {
        if (IS_POST) {
            $result = ['status' => 0, 'info' => '', 'url' => ''];

            $mobile     = I('post.mobile/s');
            $password   = I('post.password/s');
            /* 调用UC登录接口登录 */
            $user = new UserApi;
            $uid = $user->login($mobile, $password, 3);
            if(0 < $uid) { // UC登录成功
                /* 登录用户 */
                $Member = D('Common/Member');
                if ($Member->login($uid)) { //登录用户
                    $result = ['status' => 1, 'info' => $uid, 'url' => get_referer()];
                } else {
                    $result['info'] = $Member->getError();
                }
            } else { //登录失败
                switch($uid) {
                    case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
                    case -2: $error = '密码错误！'; break;
                    default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
                }
                $result['info'] = $error;
            }
            return $result;
        }
    }


    /* 执行注册操作 */
    private function register_act()
    {
        if (IS_POST) {
            $result = ['status' => 0, 'info' => '', 'url' => ''];

            $data = array(
                'password' => I('post.password/s'),
                'mobile'   => I('post.mobile/s'),
                'smsCode'   => I('post.smsCode/s'),
            );

            $business_code = 1001;
            if (!$this->check_sms($data['mobile'], $data['smsCode'], $business_code)) {
            	$result['info'] = '短信验证码不正确';
				return $result;
            }

            $User = new UserApi;
            $uid = $User->register($data);
            if (0 < $uid) { //注册成功
                // 注册成功，静默注册登录本应用
                $Member = D('Common/Member');
                if ($Member->login($uid)) {
                    //TODO: 发送验证邮件

                    $result = ['status' => 1, 'info' => $uid, 'url' => get_referer()];
                } else {
                    $result['info'] = $Member->getError();
                }
            } else { // 注册失败，显示错误信息
                $result['info'] = $User->showRegError($uid);
            }
            return $result;
        }
    }


    // 执行oauth自动登录注册操作
    private function autoOauthLoginAct($uid, $openid, $unionid, $oauth_id) {
        $result = ['status' => 0, 'info' => '', 'url' => ''];
        /* 调用UC登录接口登录 */
        $user = new UserApi;
        $uid = $user->oauthLogin($uid, $openid, $unionid, $oauth_id);
        if(0 < $uid){ // UC登录成功
            /* 登录用户 */
            $Member = D('Common/Member');
            if ($Member->login($uid)) { //登录用户

                $result = ['status' => 0, 'info' => $uid, 'url' => get_referer()];
            } else {
                $result['info'] = $Member->getError();
            }
        } else { //登录失败
            switch($uid) {
                case -1: $error = '用户不存在或被禁用！'; break; //系统级别禁用
                case -2: $error = '没有绑定不能自动登录！'; break;
                default: $error = '未知错误！'; break; // 0-接口参数错误（调试阶段使用）
            }
            $result['info'] = $error;
        }
        return $result;
    }



	/* 退出登录 */
	public function logout(){
		if(is_login()){
			D('Common/Member')->logout();
			$this->success('退出成功', U('User/login'));
		} else {
			$this->redirect('User/login');
		}
	}

	/* 验证码，用于登录和注册 */
	public function verify() {
		$verify = new \COM\Verify();
		$verify->entry(1);
	}
}
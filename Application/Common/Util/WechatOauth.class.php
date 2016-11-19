<?php
/**
 * 微信oauth
 * Update time：2016-6-21 17:47:16
 */

namespace Common\Util;

class WechatOauth {

	protected $code;
	protected $openid;
	protected $token;

	//配置数组
	public $config = array(
		'AppID'     => '',
		'AppSecret' => '',
		'curl_timeout' => 30,
	);


	//构造函数
	public function __construct($oauth_id)
	{
		$oauth = M('oauth')->find($oauth_id);
		$config = unserialize($oauth['config']);
		if (!empty($config['AppID']) && !empty($config['AppSecret'])) {
			$this->config['AppID'] = $config['AppID'];
			$this->config['AppSecret'] = $config['AppSecret'];
		} else {
			E('oauth 配置不正确！');
		}
	}


	public function __get($name)
	{
		if (isset($this->config[$name]))
			return $this->config[$name];
	}


	function setCode($code)
	{
		$this->code = $code;
	}


	// 重定向到微信获取code
	public function get_code($redirect_uri, $scope = 'snsapi_base')
	{
		header('location: ' . $this->createOauthUrlForCode($redirect_uri, $scope));
	}


	// 回调地址，作用：接收code并处理
	public function callback()
	{
		if (!empty($_GET['code'])) {
			if (!$this->check_state()) {
				return ['status' => 0, 'info' => '非法回调'];
			}
			$this->code = $_GET['code'];
			// 用户如果刷新此页面，则会使用的旧的code，会获取失败
			return $this->getAccessToken();
		} else {	// 用户取消授权

		}
	}


	// 防止CSRF欺诈攻击
	private function check_state()
	{
		if(!empty($_GET['state']) && $_GET['state'] == session('state')) {
			session('state', null);
			return true;
		} else {
			return false;
		}
	}


	// 获取Access_token
	public function getAccessToken()
	{
		$url = $this->createOauthUrlForAccessToken();
		$result = $this->http($url);
		$result = json_decode($result, true);
		$this->token = $result;
		return $result;
	}


	// 获取Openid
	public function getOpenid()
	{
		$this->openid = $this->token['openid'];
		return $this->openid;
	}


	// 获取用户信息
	public function getUserInfo($access_token, $openid)
	{
		$url = $this->createOauthUrlForUserinfo($access_token, $openid);
		$result = $this->http($url);
		return json_decode($result, true);
	}


	// 刷新access_token
	public function refreshToken($refresh_token)
	{
		$url = $this->createOauthUrlRefreshAccessToken($access_token);
		$result = $this->http($url);
		return json_decode($result, true);
	}


	// 检测access_token
	public function checkToken($access_token, $openid)
	{
		$url = $this->createOauthUrlCheckAccessToken($access_token, $openid);
		$result = $this->http($url);
		$result = json_decode($result, true);
		if (0 == $result['errcode']) {
			return true;
		} else {
			return $result;
		}
	}


	// 生成可以获取userinfo的url
	public function createOauthUrlForUserInfo($access_token, $openid)
	{
		$param = [
			'access_token' 	=> $access_token,
			'openid' 		=> $openid,
			'lang' 			=> 'zh_CN',
		];
		$bizString = $this->formatBizQueryParaMap($param);
		return "https://api.weixin.qq.com/sns/userinfo?" . $bizString;
	}


	// 生成可以获得access_token的url
	public function createOauthUrlForAccessToken()
	{
		$param = [
			'appid' 		=> $this->AppID,
			'secret' 		=> $this->AppSecret,
			'code' 			=> $this->code,
			'grant_type' 	=> 'authorization_code',
		];
		$bizString = $this->formatBizQueryParaMap($param);
		return "https://api.weixin.qq.com/sns/oauth2/access_token?" . $bizString;
	}


	// 生成获取code的url
	public function createOauthUrlForCode($redirectUrl, $scope = 'snsapi_base')
	{	
		// 防止CSRF欺诈攻击
		$state = md5(time());
		session('state', $state);
		$param = [
			"appid" 			=> $this->AppID,
			'redirect_uri' 		=> $redirectUrl,
			'response_type' 	=> 'code',
			'scope' 			=> $scope,
			'state' 			=> $state . "#wechat_redirect",
		];
		$bizString = $this->formatBizQueryParaMap($param);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?" . $bizString;
	}


	// 生成检验授权凭证（access_token）是否有效的url
	public function createOauthUrlCheckAccessToken($access_token, $openid)
	{	
		$param = [
			"access_token" 	=> $access_token,
			'openid' 		=> $openid,
		];
		$bizString = $this->formatBizQueryParaMap($param);
		return "https://api.weixin.qq.com/sns/auth?" . $bizString;
	}


	// 生成刷新access_token的url
	public function createOauthUrlRefreshAccessToken($refresh_token)
	{	
		$param = [
			"appid" 			=> $this->AppID,
			'grant_type' 		=> 'refresh_token',
			'refresh_token' 	=> $refresh_token,
		];
		$bizString = $this->formatBizQueryParaMap($param);
		return "https://api.weixin.qq.com/sns/oauth2/refresh_token?" . $bizString;
	}


	// 产生随机字符串，不长于32位
	public function createNoncestr($length = 32) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);  
		}  
		return $str;
	}
	

	// 格式化参数，签名过程需要使用
	public function formatBizQueryParaMap($paraMap, $urlencode = false)
	{
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v) {
		    if ($urlencode) {
			   $v = urlencode($v);
			}
			//$buff .= strtolower($k) . "=" . $v . "&";
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen($buff) > 0) {
			$reqPar = substr($buff, 0, strlen($buff) - 1);
		}
		return $reqPar;
	}


	// curl请求
	public function http($url) {
		//初始化curl
       	$ch = curl_init();
		//设置超时
		@curl_setopt($ch, CURLOP_TIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//运行curl，结果以jason形式返回
        $result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
}
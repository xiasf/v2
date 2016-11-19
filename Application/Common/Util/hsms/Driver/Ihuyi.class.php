<?php

namespace Common\Util\hsms\Driver;
use Common\Util\hsms\hsmsBase;

 /**
 * @class ihuyi 短信接口
 */
class ihuyi extends hsmsBase
{
	private $submitUrl = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";

	/**
	 * @brief 发送短信
	 * @param string $mobile
	 * @param string $content
	 * @return
	 */
	public function send($mobile, $content, $data = array()) {
		if (true === $data) {

		} else {
			$content = $this->content($content, $data);
		}

		$config = $this->getParam();
		$post_data = array(
			'account'   => $config['account'],
			'password'   => $config['password'],
			'mobile'   => $mobile,
			'content'     => addslashes($content),
		);
		$url = $this->submitUrl;
		$curlPost = http_build_query($post_data);	// http_build_query 自带 rawurlencode 的效果
		// $curlPost = 'account='.$post_data['account'].'&password='.$post_data['password'].'&mobile='.$post_data['mobile'].'&content='.rawurlencode($post_data['content']);

		$curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $result = curl_exec($curl);
        curl_close($curl);
		return $this->response($result);
	}

	/**
	 * @brief 解析结果
	 * @param $result 发送结果
	 * @return string success or fail
	 */
	public function response($result) {
		$array = xml_to_array($result);
		if ($array['SubmitResult']['code'] == 2) {
			return 'success';
		} else {
			return $array;
		}
	}

	/**
	 * @brief 获取配置
	 */
	public function getParam() {
		return array(
			'account' => C('HSMS_IHUYI_CONFIG.account'),
			"password"   => C('HSMS_IHUYI_CONFIG.password'),
		);
	}
}
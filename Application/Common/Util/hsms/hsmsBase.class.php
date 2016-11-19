<?php
namespace Common\Util\hsms;

/**
 * @brief 短信抽象类
 */
abstract class hsmsBase {
	
	public function content($content, $data) {
		$conf = include __DIR__ . '/Conf/tpl.php';
		if (isset($conf[$content]))
			return strtr($conf[$content], $data);
		else
			return '';
	}

	//短信发送接口
	abstract public function send($mobile, $content, $data = array());

	//短信发送结果接口
	abstract public function response($result);

	//短信配置参数
	abstract public function getParam();
}
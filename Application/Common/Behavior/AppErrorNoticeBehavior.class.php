<?php
/**
 * 错误推送行为
 * Update time：2016-4-13 10:05:16
 */
namespace Common\Behavior;
use Common\Util\hsms\hsms;
use Core\Log;
class AppErrorNoticeBehavior {
	public function run(&$params) {
		$http_referer = isset($_SERVER['HTTP_REFERER']) ? str_replace('http://', '', $_SERVER['HTTP_REFERER']) : '';
		// hsms::send(15997152146, 'app_error_notice', array('{error}' => $params, '{url}' => APP_DOMAIN . __SELF__ . ' [HTTP_REFERER]' . $http_referer, '{time}' => date('Y-m-d H:i:s', NOW_TIME)));
		$destination = dirname(C('LOG_PATH')) . '/Error/' . date('y_m_d') . '.log';
		Log::write($params, Log::ERROR, '', $destination);
	}
}
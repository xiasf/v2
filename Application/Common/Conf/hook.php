<?php
/**
 * 应用 自定义钩子
 * Update time：2015-3-7 13:50:34
 */

return array(

	// 应用初始化钩子
	'app_init'=>array(
		'Common\Behavior\InitHookBehavior',	// 追加一个应用初始化行为
	),

	// 应用开始钩子
	'app_begin' => array(
		'Common\Behavior\OrderStatusBehavior',	// 订单状态拦截行为
		'Common\Behavior\OauthLoginBehavior',	// 自动登录拦截行为
	),

	// 系统错误钩子
	'app_error' => array(
        'Common\Behavior\AppErrorNoticeBehavior', // 错误推送行为
    ),
);


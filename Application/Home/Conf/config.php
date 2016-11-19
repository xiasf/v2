<?php
/**
 * 模块 默认的配置文件
 * Update time：2015-3-7 13:50:34
 */

defined('APP_NAME') or exit;				// 拦截非法访问
$_GET['device'] = 'wap';

return  array(

    'AUTOLOAD_NAMESPACE' => array(			// 注册自己的命名空间
    	'My'     => PATH . 'My',
    	'One'    => PATH . 'One',
	),

    'SHOW_PAGE_TRACE' => false,
    // 'TMPL_STRIP_SPACE'      =>  false,

    'DEFAULT_DEVICE' => 'wap',	// 设置默认设备


    // SEO配置 带优先级，没有定义的值会取上一级（嘻嘻，真好用）
    'SEO' => array(
        'home' => array(
            'title'       => '首页 - 五味真火',
            'keywords'    => '五味真火,黄州外卖',
            'description' => 'baby I love you',
            'head_title'  => '黄冈',
        ),
        'home/area' => array(
            'title'       => '选地址 - 五味真火',
            'keywords'    => '五味真火,黄州外卖',
            'description' => 'baby I love you',
            'head_title'  => '黄冈',
        ),
        'home/area/index' =>array(
            'title'       => '选地址 - 五味真火',
            'keywords'    => '',
            'description' => '',
        ),
        'home/area/check' => array(
            'title'       => '{address}附近的美食 - 五味真火',
            'head_title'  => '{address} - 附近',
        ),
        'home/choose/index' => array(
            'title'       => '{shop_name} - 五味真火',
            'head_title'  => '{shop_name}',
        ),
        'home/cart/checkout' => array(
            'title'       => '确认订单 - 五味真火',
            'head_title'  => '确认订单',
        ),
        'home/cart' => array(
            'title'       => '美食篮 - 五味真火',
            'head_title'  => '美食篮',
        ),
        'home/cart/pay' => array(
            'title'       => '支付订单 - 五味真火',
            'head_title'  => '支付订单',
        ),
    ),
    
);
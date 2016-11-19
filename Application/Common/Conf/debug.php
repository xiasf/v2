<?php
/**
 * 应用 默认的调试模式配置文件
 * Update time：2016-4-17 12:27:14
 */

defined('APP_NAME') or exit;				// 拦截非法访问
return array(

    /* 数据库相关设置 */
    'DB_DEBUG'           =>    true,        // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'    =>    false,      // 是否启用字段缓存

	/* 模板相关设置 */
	'TPL_CACHE_ON'         	=> false, 		// 是否开启模板编译缓存,设为false则每次都会重新编译
	'TMPL_STRIP_SPACE'      =>  true,	// 是否压缩模板输出
    'TMPL_DENY_PHP' 		=> false,	// 模板中是否允许使用php标签

    // 'URL_MODEL' => 0,
);
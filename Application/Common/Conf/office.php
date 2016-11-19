<?php
/**
 * 应用 office状态 配置文件
 * Update time：2015-3-7 13:50:34
 */
defined('APP_NAME') or exit;				// 拦截非法访问

return array(

	// 加载其他配置文件
	'LOAD_EXT_CONFIG'    =>    'office_db',

    // 资源路径设置
    'TMPL_PARSE_STRING' => array(
        '__H__' => 'http://static.hbdscy.com/H+',   // H+模板资源文件夹
        '__STATIC__' => 'http://static.hbdscy.com',   // 静态资源目录
    ),

    // UC服务
    'UC_APP_ID' 		=> 1,
    'UC_API_TYPE' 		=> 'Model',
    'UC_AUTH_KEY' 		=> '(*FB6IJ8n:_%aUkQNHOto4lR$]9!mw27bd-Xu3"j',
	'UC_DB_DSN' 		=> 'mysql://root:qq5752020@127.0.0.1:3306/v2',
	'UC_TABLE_PREFIX' 	=> 'v2_',
);
<?php
/**
 * UCenter客户端配置文件
 * 注意：该配置文件请使用常量方式定义
 */

define('UC_APP_ID', C('UC_APP_ID')); 			// 应用ID
define('UC_API_TYPE', C('UC_API_TYPE')); 		// 可选值 Model / Service
define('UC_AUTH_KEY', C('UC_AUTH_KEY')); 		// 加密KEY
define('UC_DB_DSN', C('UC_DB_DSN')); 			// 数据库连接，使用Model方式调用API必须配置此项
define('UC_TABLE_PREFIX', C('v2_')); 			// 数据表前缀，使用Model方式调用API必须配置此项

<?php
/**
 *┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
 *┃						[应用入口文件]  	  		 		   	  ┃
 *┣━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┫
 *┃^_^  ^_^  ^_^ ^_^  ^_^  ^_^  ^_^  ^_^  ^_^  ^_^ ^_^ ^_^   ^_^  ^_^┃
 *┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛
 */
define('PATH', __DIR__ . '/');

define('SF_PATH', PATH . '../SF/');

// define('APP_NAME', 'Application');

define('TMPL_PATH', PATH . 'templates/');

// define('TMPL_CACHE_PATH', PATH.'tpl_c/');

// define('APP_STATUS', 'office');

define('APP_DEBUG', true);

require SF_PATH . 'setup.php';

// require  '../sf/setup.php';
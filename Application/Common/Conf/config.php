<?php
/**
 * 应用 默认的配置文件
 * Update time：2015-3-7 13:50:34
 */
defined('APP_NAME') or exit;				// 拦截非法访问
return array(

    /* 数据库相关设置 */
    'DB_DEBUG'           =>    false,        // 数据库调试模式 开启后可以记录SQL日志
    'DB_FIELDS_CACHE'    =>    true,        // 是否启用字段缓存

    // 加载其他配置文件
    'LOAD_EXT_CONFIG'    =>    'db',

    // UC服务
    'UC_APP_ID'         => 1,
    'UC_API_TYPE'       => 'Model',
    'UC_AUTH_KEY'       => '(*FB6IJ8n:_%aUkQNHOto4lR$]9!mw27bd-Xu3"j',
    'UC_DB_DSN'         => 'mysql://root:root@127.0.0.1:3306/v2',
    'UC_TABLE_PREFIX'   => 'v2_',

    /* 杂项设置 */
    'DEFAULT_FILTER'        =>  'optimize_htmlspecialchars',    // 优化的过滤方法 用于I函数...
    'WORDS_DRIVER' => 'scws',   // 分词驱动

    /* 图片上传相关配置 */
    'PIC_UPLOAD' => array(
        'mimes'    => '',
        'maxSize'  => 2*1024*1024,
        'exts'     => 'jpg,gif,png,jpeg',
        'autoSub'  => true,
        'subName'  => array('date', 'Y-m-d'),
        'rootPath' => PATH,
        'savePath' => 'Uploads/Pic/',
        'saveName' => array('uniqid', ''),
        'saveExt'  => '',
        'replace'  => false,
        'hash'     => true,
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）

    'PIC_UPLOAD_DRIVER' => 'local',             // 图片上传驱动

    // 短信配置
    'HSMS_DRIVER'   => 'ihuyi',
    
    'HSMS_IHUYI_CONFIG' => array(
        'account' => 'cf_wwzh',
        'password' => 'qq5752020'
    ),

    /* 用户相关设置 */
    'USER_ADMINISTRATOR' => array(1), //管理员用户ID

    // 资源路径设置
    'TMPL_PARSE_STRING' => array(
        '__H__' => 'http://static.baby.com/H+',   // H+模板资源文件夹
        '__STATIC__' => 'http://static.baby.com',   // 静态资源目录
    ),

    // 布局设置
    'LAYOUT_ON'     => true,
    'LAYOUT_NAME'   => 'site',

    'TMPL_ACTION_SUCCESS'   =>  'Public:dispatch_jump_ok',  // 成功跳转
    'TMPL_ACTION_ERROR'     =>  'Public:dispatch_jump_no',  // 失败跳转

    // beecloud支付参数
    'BEECLOUD' => array(
        'appSecret'      => '455e49f1-eb72-4c52-a820-fa90bfd47eec',
        'app_id'         => '47892758-47f2-41bf-9cb3-fb9afd87de03',
        'return_url'     => 'http://hbdscy.com/v1/index.php?a=remindorder',
        'show_url'       => 'http://www.hbdscy.com',
        'bill_no_prefix' => '520whzhv2',
    ),

    'ORDER_TIMEOUT' => 15*60,   // 订单超时时间


    /* session相关设置 */
    'SESSION_AUTO_START'     => true,           // 是否自动开启Session
    'SESSION_OPTIONS'        => array(
        'name' => 'SID'
        // 'path' => 'D:\wamp\www\tem'
        // 'domain' => 'www.baby.com'
        // 'expire' => '86400'
        // 'use_trans_sid' => ''
        // 'use_cookies' => ''
        // 'cache_limiter' => ''
        // 'cache_expire' => ''
        // 'type' => ''
    ),
    'SESSION_PREFIX'         => '',             // session 前缀
    'VAR_SESSION_ID' => 'session_id',   // sessionID的提交变量 修复uploadify插件无法传递session_id的bug


    /* cookie相关设置 */
    // 'COOKIE_EXPIRE'         =>  0,              // Cookie有效期
    // 'COOKIE_DOMAIN'         =>  '',             // Cookie有效域名
    // 'COOKIE_PATH'           =>  '/',            // Cookie路径
    // 'COOKIE_PREFIX'         =>  '',             // Cookie前缀 避免冲突
    // 'COOKIE_SECURE'         =>  false,          // Cookie安全传输
    // 'COOKIE_HTTPONLY'       =>  '',             // Cookie httponly设置
);
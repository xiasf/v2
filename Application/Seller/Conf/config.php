<?php
return array(
	'SHOW_PAGE_TRACE' => false,
	// 'TMPL_STRIP_SPACE'      =>  false,

    'SESSION_PREFIX' => 'seller',             // session 前缀

    // SEO配置 带优先级，没有定义的值会取上一级（嘻嘻，真好用）
    'SEO' => array(
        'seller' => array(
            'title'       => '商户后台 - 五味真火',
            'keywords'    => '',
            'description' => 'baby I love you',
            'head_title'  => '',
        ),
    ),
);
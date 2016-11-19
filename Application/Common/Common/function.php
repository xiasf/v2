<?php
/**
 * 应用 公共函数
 * @category Application
 * @package  Common
 * Update time：2016-6-7 09:21:53
 */

/**
 * 高级过滤器
 * @param  midx  $data 要过滤数据
 * @param  string  $type 过滤方式
 * @return midx  $data 过滤后的数据
 */
function filter() {
        // require_once(dirname(__FILE__)."/htmlpurifier/HTMLPurifier.standalone.php");
        // $cache_dir=IWeb::$app->getRuntimePath()."htmlpurifier/";

        // if(!file_exists($cache_dir))
        // {
        //     IFile::mkdir($cache_dir);
        // }
        // $config = HTMLPurifier_Config::createDefault();

        // //配置 允许flash
        // $config->set('HTML.SafeEmbed',true);
        // $config->set('HTML.SafeObject',true);
        // $config->set('HTML.SafeIframe',true);
        // $config->set('Output.FlashCompat',true);

        // //配置 缓存目录
        // $config->set('Cache.SerializerPath',$cache_dir); //设置cache目录

        // //允许<a>的target属性
        // $def = $config->getHTMLDefinition(true);
        // $def->addAttribute('a', 'target', 'Enum#_blank,_self,_target,_top');

        // //过略掉所有<script>，<i?frame>标签的on事件,css的js-expression、import等js行为，a的js-href
        // $purifier = new HTMLPurifier($config);
        // return $purifier->purify($str);
}


// 获取访客的可粒度标示
function get_granularity() {
    $userKey = cookie("granularity");
    if (empty($userKey)) {
        $chars='abcdefghijklmnopqrstuvwxyz0123456789';
        mt_srand((double)microtime()*1000000*getmypid());
        $rndStr='';
        while(strlen($rndStr)<11)$rndStr.=substr($chars,(mt_rand()%strlen($chars)),1);
        $userKey = time() .$rndStr;
        cookie("granularity",$userKey,3650);
    }
    return $userKey;
}


// 优化的过滤方法 用于I函数...（解决原来不能过滤单引号的问题）
function optimize_htmlspecialchars($str) {
    return htmlspecialchars($str, ENT_QUOTES);
}

/**
 * 动态输出SOE信息 依赖配置
 * @param  string  $v seo键
 * @return string  $content seo信息
 */
function seo($v) {
    $seo_config = C('SEO');
    if (isset($seo_config[strtolower(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME)][$v])) {
        $content = $seo_config[strtolower(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME)][$v];
    } elseif (isset($seo_config[strtolower(MODULE_NAME . '/' . CONTROLLER_NAME)][$v])) {
        $content = $seo_config[strtolower(MODULE_NAME . '/' . CONTROLLER_NAME)][$v];
    } elseif (isset($seo_config[strtolower(MODULE_NAME)][$v])) {
        $content = $seo_config[strtolower(MODULE_NAME)][$v];
    } else {
        $content = '';
    }
    if ($content) {
        $viewObj = Core\SF::instance('Core\View');
        $content = preg_replace_callback('/{(\S+?)}/', function($matches) use($viewObj) {
            return $viewObj->get($matches[1]);
        }, $content);
    }
    return $content;
}

// 判断是否是微信
function is_wechat() {
    return (boolean) preg_match('/.*?(MicroMessenger\/([0-9.]+))\s*/',  $_SERVER['HTTP_USER_AGENT'], $matches);
}


/**
 * 输出微缩图（获取微缩图输出地址）（性能相对高些，因为真实访问时毕竟是要输出图片流的）
 * @param  string  $src 图片url 从数据库直接读出来的 Uploads/Pic/2016-03-04/6sdfb345bds43f.png
 * @return int  $width 宽
 * @return int  $height 高
 * @return string  $format 格式
 * @return int  $type 类型
 * @return int  $q 质量
 * @return int  $s 是否生成缓存
 * @return int  $o 是否使用缓存
 * @return string  图片输出地址 Home/Pic/thumb/img/Uploads/Pic/2015-12-07/566523b7062dc.jpg/w/150/h/150/f/png/t/3/q/50/s/2/o/2
 */
function thumb($src, $width = 60, $height = 60, $format = 'jpg', $type = 2, $q = 80, $s = 1, $o = 1) {
    $array = array(
        'w'     => $width ? : 0,
        'h'     => $height ? : 0,
        'f'     => $format,
        't'     => $type,
        'q'     => $q,
        's'     => $s,
        'o'     => $o
    );
    if ('jpg' == $format) unset($array['f']);
    if (2 == $type)       unset($array['t']);
    if (80 == $q)          unset($array['q']);
    if (1 == $s)          unset($array['s']);
    if (1 == $o)          unset($array['o']);
    C('URL_CASE_INSENSITIVE', false);
    return U('Home/Pic/thumb/img/' . $src, $array, false);
}


/**
 * 获取微缩图地址src（性能相对高一些，输出的是真实图片地址）
 * @param  string  $src 图片url 从数据库直接读出来的 Uploads/Pic/2016-03-04/6sdfb345bds43f.png
 * @return int  $width 宽
 * @return int  $height 高
 * @return string  $format 格式
 * @return int  $type 类型
 * @return int  $q 质量
 * @return string  图片输出地址 /_thumb/Uploads/Pic/2016-03-21/56eee5d54bc61_20-20-jpg-2-80.jpg
 */
function get_thumb_src($src, $width = 60, $height = 60, $format = 'jpg', $type = 2, $q = 80) {
    $width = $width ? : 0;
    $height = $height ? : 0;
    return A('Home/Pic')->get_thumb_src($src, $width, $height, $format, $type, $q);
}


/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 */
function check_verify($code, $id = 1){
    $verify = new \Core\Verify();
    return $verify->check($code, $id);
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login() {
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 */
function is_administrator($uid = null) {
    $uid = is_null($uid) ? is_login() : $uid;
    return $uid && in_array(intval($uid), C('USER_ADMINISTRATOR'), true);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}


/**
 * XML解析
 * @param string  $data 数据
 * @return array
 */
function xml_to_array($xml){
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if(preg_match_all($reg, $xml, $matches)){
        $count = count($matches[0]);
        for($i = 0; $i < $count; $i++){
        $subxml= $matches[2][$i];
        $key = $matches[1][$i];
            if(preg_match( $reg, $subxml )){
                $arr[$key] = xml_to_array( $subxml );
            }else{
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}


// 获取客户端设备信息
function get_client_drive($UserAgent = '') {
    if ($UserAgent == '')
         $UserAgent = $_SERVER['HTTP_USER_AGENT'];

    $client_info  = array();

    if(preg_match('/(firefox|opera|ucbrowser|ubrowser|chrome|msie|safari).?([0-9]\.?)*/i', $UserAgent, $browser))
    $browser = $browser[0];
    else
    $browser = '未知';
    $client_info[] = $browser;

    /*浏览器检查完毕，开始检查设备*/

    if (is_int(stripos($UserAgent, 'Windows'))) {
        if (is_int(stripos($UserAgent, 'Windows NT 6.2'))) {
            $drive = 'Windows 8';
        if (is_int(stripos($UserAgent, 'WOW64')))
            $drive .= ' 64bit';
        else
            $drive .= " 32bit";
        } elseif (is_int(stripos($UserAgent, 'Windows NT 6.1'))) {
            $drive = 'Windows 7';
        if (is_int(stripos($UserAgent, 'WOW64')))
            $drive .= ' 64bit';
        else
            $drive .= " 32bit";
        } elseif (is_int(stripos($UserAgent, 'Windows NT 6.0'))) {
            $drive = 'Windows Vista';
        } elseif (is_int(stripos($UserAgent, 'Windows NT 5.2'))) {
            $drive = 'Windows 2003';
        } elseif (is_int(stripos($UserAgent, 'Windows NT 5.1'))) {
            $drive = 'Windows XP';
        } elseif (is_int(stripos($UserAgent, 'Windows NT 5.0'))) {
            $drive = 'Windows 2000';
        }
        $client_info[] = $drive;
        return $client_info;
    }

    if (is_int(stripos($UserAgent, 'Macintosh'))) {
         if (preg_match('/(Mac OS).?\w?.?([0-9]\.?)*/i', $UserAgent, $Macintosh)) {
            $drive = $Macintosh[0];
        }
        $client_info[] = $drive;
        return $client_info;
    }

    elseif (is_int(stripos($UserAgent, 'iPhone'))) {
        if (preg_match('/(iPhone OS).?([0-9]_?)*/i', $UserAgent, $iPhone)) {
            $drive = str_replace('_', '.', $iPhone[0]) ;
        }
        $client_info[] = $drive;
        return $client_info;
    }

    elseif (is_int(stripos($UserAgent, 'iPad'))) {
         if (preg_match('/(OS).?([0-9]_?)*/i', $UserAgent, $iPad)) {
            $drive = str_replace('_', '.', $iPad[0]) ;
        }
        $client_info[] = 'iPad ' . $drive;
        return $client_info;
    }

    elseif (is_int(stripos($UserAgent, 'Android'))) {
       if(is_int(stripos($UserAgent, 'Galaxy Nexus')))
            $drive = 'Galaxy Nexus '; 
       if (is_int(stripos($UserAgent, 'Nexus S')))
            $drive = 'Nexus S ';  
       if (preg_match('/(Android).?([0-9]\.?)*/i', $UserAgent, $Android)) {
            $drive .= $Android[0] ;
        }
        $client_info[] = $drive;
        return $client_info;
    }

    elseif (is_int(stripos($UserAgent, 'Adr'))) {
       if(is_int(stripos($UserAgent, 'Galaxy Nexus')))
            $drive = 'Galaxy Nexus '; 
       if (is_int(stripos($UserAgent, 'Nexus S')))
            $drive = 'Nexus S ';  
       if (preg_match('/(Adr).?([0-9]\.?)*/i', $UserAgent, $Adr)) {
            $drive .= str_replace('Adr', 'Android', $Adr[0]) ;
        }
        $client_info[] = $drive;
        return $client_info;
    }

    elseif (is_int(stripos($UserAgent, 'MeeGo'))) {
        if(preg_match('/(Nokia).?(([0-9]\.?)*)/i', $UserAgent, $MeeGo)) {
            $drive = 'MeeGo Nokia ' . $MeeGo[2] ;
        }
        $client_info[] = $drive;
        return $client_info;
    }

    elseif (is_int(stripos($UserAgent, 'BlackBerry'))) {
        if(preg_match('/(BlackBerry ).?([0-9]\.?)*/i', $UserAgent, $BlackBerry)) {
            $drive = $BlackBerry[0];
        }
        $client_info[] = $drive;
        return $client_info;
    }

    elseif (is_int(stripos($UserAgent, 'PlayBook'))) {     //可以看到双括号时以外括号为准
        if (preg_match('/(RIM Tablet OS).?(([0-9]\.?)*)/i', $UserAgent, $PlayBook)) {
            $drive = 'BlackBerry PlayBook ' . $PlayBook[2];
        }
        $client_info[] = $drive;
        return $client_info;
    }

    $drive = '未知';
    $client_info[] = $drive;
    return $client_info;
}

// 根据ID获取商店类别名称
function  get_shop_type($id = null) {
    $arr = array('', '快餐', '烧烤', '烧烤半成品', '水果超市');
    if ($id)
        return isset($arr[$id]) ? $arr[$id] : '';
    else {
        unset($arr[0]);
        return $arr;
    }
}


// 解析订单状态
function  parse_order_status($status = 0) {
    $arr = array(
        0 => '待确认',
        1 => '已确认',
        2 => '已取消',
        3 => '已作废',
        4 => '待配送',
        5 => '配送中',
        6 => '已送达',
        7 => '申请退款',
        8 => '退款中',
        9 => '已退款',
        10 => '超时关闭',
    );
    return isset($arr[$status]) ? $arr[$status] : '';
}

// 解析订单打印状态
function  parse_order_print_status($status = 0) {
    $arr = array(
        0 => '未推送',
        1 => '已推送',
        2 => '推送失败',
        3 => '已打印',
        4 => '打印失败',
    );
    return isset($arr[$status]) ? $arr[$status] : '';
}

function timeDiff($time ,$precision = false) {
    $date = new Org\Util\Date();
    return $date->timeDiff($time, $precision);
}


// 获取“登录前一页面”
function get_referer($k = 'redirectURL', $u = '', $urlencode = false) {
    $url = isset($_REQUEST[$k]) ? $_REQUEST[$k] : '';
    $http_referer = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '';
    $url = $url ? : $http_referer;
    $u = $u ? : U('Index/index');
    $r =  ($url && false === stripos($url, __SELF__) && false === stripos($url, 'logout')) ? $url : $u;
    if ($urlencode) {
        return urlencode($r);
    } else {
        return $r;
    }
}
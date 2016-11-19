<?php
namespace Common\Util\hsms;

class hsms {

    /**
     * 操作句柄
     * @var string
     * @access protected
     */
    static protected $handler;

    static public function get_handler($type = '', $options = array()) {
        if(empty($type)) $type = C('HSMS_DRIVER');
        static $instance = array();
        if (!isset($instance[$type])) {
            $class  =   'Common\\Util\\hsms\\Driver\\' . ucwords($type);
            $instance[$type] = new $class($options);
        }
        return self::$handler = $instance[$type];
    }

    static public function __callstatic($method,$args) {
        self::get_handler();
        if (method_exists(self::$handler, $method)) {
           return call_user_func_array(array(self::$handler, $method), $args);
        }
    }
}
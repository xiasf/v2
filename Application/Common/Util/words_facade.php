<?php
/**
 * @file words_facade.php
 * @brief 分词类
 */
class words_facade
{
	public static $instance  = null;

	/**
	 * @brief 创建分词类库实例
	 */
	private static function createInstance($type = '', $options = array()) {
		if(empty($type)) $type = C('WORDS_DRIVER');
        static $instance = array();
        if (!isset($instance[$type])) {
            $class  =   'Common\\Util\\words\\Driver\\' . ucwords($type);
            $instance[$type] = new $class($options);
        }
        return self::$instance = $instance[$type];
	}

	/**
	 * @brief 运行分词
	 * @param string $content 要分词的内容
	 * @return array 词语
	 */
	public static function run($content) {
		$instance = self::createInstance();
		if($instance) {
			return $instance->run($content);
		}
		return $content;
	}
}
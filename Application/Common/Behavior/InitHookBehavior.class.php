<?php
/**
 * 应用初始化行为
 * Update time：2016-4-13 10:05:16
 */
namespace Common\Behavior;
class InitHookBehavior {
	public function run(&$params) {
		// C(include CONF_PATH .'db.php');	// 读取数据库配置
        // set_app_hook();
	}

	// 设置应用自定义钩子
	public function set_app_hook() {
		// 钩子在数据库里面
		$data = S('hooks');
        if (!$data) {
            $hooks = M('Hooks')->getField('name,addons');
            foreach ($hooks as $key => $value) {
                if ($value) {
                    $map['status']  =   1;
                    $names          =   explode(',', $value);
                    $map['name']    =   array('IN', $names);
                    $data = M('Addons')->where($map)->getField('id,name');
                    if ($data) {
                        $addons = array_intersect($names, $data);
                        Hook::add($key, $addons);
                    }
                }
            }
            S('hooks', Hook::get());
        } else {
            Hook::import($data, false);
        }
	}
}
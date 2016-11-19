<?php
namespace Common\Controller;
use Core\Controller;
use Core\Auth;

class AuthController extends Controller {

	// 访问授权
	protected function _initialize() {

        define('UID', is_login());
        if (!UID) {
            $this->redirect('Public/login');
        }

        define('IS_ROOT', is_administrator());
		
	    $rule = strtolower(MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME);
	    if (!$this->checkRule($rule)) {
	        $this->error('提示:无权访问,您可能需要联系管理员为您授权!');
	    }
	}


	/**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     */
    protected function checkRule($rule, $uid = UID, $type = 1, $mode = 'url'){
        if (IS_ROOT) {
            return true;
        }
        static $Auth    =   null;
        if (!$Auth) {
            $Auth       =   new Auth();
        }
        if (!$Auth->check($rule, $uid, $type, $mode)) {
            return false;
        }
        return true;
    }
}
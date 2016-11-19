<?php
/**
 * 后台模块index控制器
 * Update time：2014-5-22 16:38:43
 */
namespace Seller\Controller;

class IndexController extends BaseController {
	public function index() {
		$this->seller = M('seller')->field(true)->where(array('uid' => UID))->find();
		$this->display();
	}
}
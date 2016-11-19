<?php
namespace Home\Controller;
/**
 * 首页index控制器
 * Update time：2015-12-3 22:18:28
 */
class AreaController extends HomeController {
	public function index() {
		$this->display();
	}

	public function check() {
		if (!$this->lng     = I('cookie.lng/s','','strip_tags')) $this->redirect('index');
		if (!$this->lat     = I('cookie.lat/s','','strip_tags')) $this->redirect('index');
		if (!$this->address = I('cookie.address/s','','strip_tags')) $this->redirect('index');
		$this->display();
	}
}
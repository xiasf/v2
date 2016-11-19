<?php
/**
 * 后台模块控制器
 * Update time：2016-6-12 22:23:47
 */
namespace Seller\Controller;
use Core\Controller;

class BaseController extends Controller {

    // 访问授权
    protected function _initialize() {
        define('UID', is_login());
        if (!UID) {
            $this->redirect('Public/login');
        }
        $this->seller = array('seller_id' => session('user_auth.seller_id'));
    }

	// 定义一些基本的方法

	// 图片上传
    public function img_upload() {
        $return = A('Common/File')->uploadPic();
        $file = I('request.file/s', 'file');
        if (isset($return['data'][$file])) {
            $return['data'][$file]['url'] = __ROOT__ . '/' . $return['data'][$file]['url'];
            $return = array_merge($return, $return['data'][$file]);
            unset($return['data']);
        }
        $this->ajaxReturn($return);
    }
}
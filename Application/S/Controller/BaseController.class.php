<?php
/**
 * 后台模块控制器
 * Update time：2016-4-25 01:27:09
 */
namespace S\Controller;
use Common\Controller\AuthController;

class BaseController extends AuthController {
    
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
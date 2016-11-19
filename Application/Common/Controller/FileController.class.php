<?php
/**
 * 文件控制器
 * 主要用于下载模型的文件上传和下载
 */
namespace Common\Controller;
use Core\Controller;
class FileController extends Controller {

    /**
     * 上传图片
     */
    public function uploadPic() {
        $result = array('status' => 1, 'info' => '上传成功', 'data' => '');
        $pic = D('Common/Pic');
        $pic_driver = C('PIC_UPLOAD_DRIVER');
        $info = $pic->upload(
            $_FILES,
            C('PIC_UPLOAD'),
            C('PIC_UPLOAD_DRIVER'),
            C("UPLOAD_{$pic_driver}_CONFIG")
        );
        if ($info) {
            $result['status'] = 1;
            $result['data'] = $info;
        } else {
            $result['status'] = 0;
            $result['info']   = $pic->getError();
        }
        return $result;
    }
}
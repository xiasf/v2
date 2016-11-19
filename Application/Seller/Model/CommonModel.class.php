<?php
/**
 * S模块 公共模型
 * Update time：2016-4-22 17:12:00
 */

namespace Seller\Model;
use Core\Model;

class CommonModel extends Model {

    protected function check_img($img) {
        $pic = M('pic');
        if ($pic->field('id')->where(array('url' => $img))->find())
            return true;
        else
            return false;
    }

    protected function get_status($status) {
        return (int) $status;
    }
}
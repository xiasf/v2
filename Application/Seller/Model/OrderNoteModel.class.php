<?php
/**
 * S模块 订单模型
 * Update time：2016-5-6 22:17:03
 */

namespace Seller\Model;
use Core\Model;

class OrderNoteModel extends Model {

    protected $insertFields = 'order_id,note';
	protected $updateFields = 'order_id,note';

    protected $_validate = array(
    	array('order_id', 'check_order_id', '订单不合法', self::EXISTS_VALIDATE, 'callback'),
    );

	protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('uid', 'is_login', self::MODEL_INSERT, 'function'),
    );

    // 检测订单是否合法
    protected function check_order_id($order_id) {
        $order = M('order');
        if ($order->field('id')->find($order_id))
            return true;
        else
            return false;
    }


    // 添加备注（备注暂时只允许添加，不允许修改删除）
    public function add_note() {
    	if ((!$data = $this->create()) || !$new_id = $this->add()) {
            $this->error = $this->error ? : '添加失败';
            return false;
        } else {
        	$data['id'] = $new_id;
        	return $data;
        }
    }
}
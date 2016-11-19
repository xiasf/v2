<?php
/**
 * 商店 - 打印机 模型
 * Update time：2016-5-7 15:08:15
 */

namespace S\Model;
use Core\Model;

class ShopPrinterModel extends Model {

    protected $insertFields = 'shop_id,printer_id';
    protected $updateFields = 'shop_id,printer_id';

    protected $_validate = array(
    	array('printer_id', 'check_printer_id', '打印机不合法', self::EXISTS_VALIDATE, 'callback'),
    );


    // 检测打印机是否合法
    protected function check_printer_id($printer_id) {
        $printer = M('printer');
        if ($printer->field('id')->find($printer_id))
            return true;
        else
            return false;
    }


    // 解除商店，打印机的关系（由商店单方面解缔约）
    public function shop_extend_del($shop_id) {
		if (false === $this->where(array('shop_id' => $shop_id))->delete()) {
            $this->error = $this->error ? : '解绑失败';
            return false;
        } else {
        	return true;
        }
    }


    // 解除打印机，打印机的关系（由打印机单方面解缔约）
    public function printer_extend_del($printer_id) {
        if (false === $this->where(array('printer_id' => $printer_id))->delete()) {
            $this->error = $this->error ? : '解绑失败';
            return false;
        } else {
            return true;
        }
    }


    // 解除打印机，打印机的关系（由商店，打印机共同解缔约）
    public function shop_printer_extend_del($shop_id, $printer_id) {
        if (false === $this->where(array('shop_id' => $shop_id, 'printer_id' => $printer_id))->delete()) {
            $this->error = $this->error ? : '解绑失败';
            return false;
        } else {
            return true;
        }
    }


    // 缔约，由商店缔约（所以上面自动验证的是打印机）
    public function extend_add($data) {
        if ($this->where($data)->find()) {
            $this->error = '你已经绑定此打印机！';
            return false;
        }
		if (!$this->create($data) || !$this->add()) {
            $this->error = $this->error ? : '绑定失败';
            return false;
        } else {
        	return true;
        }
    }
}
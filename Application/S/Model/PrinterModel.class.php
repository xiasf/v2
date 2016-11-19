<?php
/**
 * 打印机模型
 * Update time：2016-3-1 23:37:18
 */

namespace S\Model;
use S\Server\OrderPrintServer;

class PrinterModel extends CommonModel {

    protected $insertFields = 'machine_code,printname,mobilephone,msign,status,create_time,create_ip';
    protected $updateFields = 'id,is_del,status,login,last_login_ip,last_login_time';

    protected $_validate = array(
        array('printname', '1,15', '打印机名称不合法', self::EXISTS_VALIDATE, 'length'),
        // array('imei', '/^\d{15}$/', '打印机IMEI格式错误', self::EXISTS_VALIDATE, 'regex'),
        // array('imsi', '/^\d{15}$/', 'SIM卡IMSI格式错误', self::VALUE_VALIDATE, 'regex'),
        // array('mobilephone', 'mobile', '打印机内部的手机号不合法', self::VALUE_VALIDATE),
        // array('msign', '/^\d{6,}$/', '打印机密码格式错误', self::VALUE_VALIDATE, 'regex'),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('create_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('update_time', NOW_TIME, self::MODEL_UPDATE),
        array('status', 'get_status', self::MODEL_BOTH, 'callback'),
    );


    // 远程终端打印测试（暂不记录数据库）
    public function send_check($id, $content) {
        $result = array('status' => 0, 'info' => '');
        if ($printer_info = $this->field('machine_code,msign')->find($id)) {
            $OrderPrintServer = new OrderPrintServer;
            $result = $OrderPrintServer->print_send($content, $printer_info['machine_code'], $printer_info['msign']);
        } else {
            $result['info'] = '设备不存在';
        }
        return $result;
    }


    // 远程终端检测
    public function printer_check($id) {
        $result = array('status' => 0, 'info' => '');
        if ($printer_info = $this->field('machine_code,msign')->find($id)) {
            $OrderPrintServer = new OrderPrintServer;
            $result = $OrderPrintServer->get_printer_status($printer_info['machine_code'], $printer_info['msign']);
        } else {
            $result['info'] = '设备不存在';
        }
        return $result;
    }

    public function printer_add() {
        if (IS_POST) {
            $this->startTrans();
            if ((!$data = $this->create()) || !$this->add()) {
                $this->rollback();
                $this->error = $this->error ? : '添加失败';
                return false;
            } else {
                $arr = array(
                    'machine_code' => $data['machine_code'],
                    'msign' => $data['msign'],
                    'mobilephone' => $data['mobilephone'],
                    'printname' => $data['printname']
                );

                // 远程终端添加
                $OrderPrintServer = new OrderPrintServer;
                $result = $OrderPrintServer->add_printer($arr);
                if (1 != $result['status']) {
                    $this->rollback();
                    $this->error = $result['info'];
                    return false;
                }
                $this->commit();
                return true;
            }
        }
    }


    public function printer_update() {
        if (IS_POST) {
            if (!$this->field('id')->find(I('post.id/d'))) {
                $this->error = '打印机不存在';
                return false;
            }
            $this->startTrans();
            if (!$this->create() || false === $this->save()) {
                $this->rollback();
                $this->error = $this->error ? : '更新失败';
                return false;
            } else {

                // 远程终端更新

                $this->commit();
                return true;
            }
        }
    }


    // 非物理删除
    public function printer_delete($id) {
        if (IS_POST) {
            if (!$this->field('id')->find($id)) {
                $this->error = '打印机不存在';
                return false;
            }
            $this->startTrans();
            if (false === $this->save(array('id' => $id, 'is_del' => 1))) {
                $this->rollback();
                $this->error = $this->error ? : '删除失败';
                return false;
            } else {

                // 远程终端删除


                $this->commit();
                return true;
            }
        }
    }


    // 彻底物理删除
    public function true_printer_delete($id) {
        if (IS_POST) {
            if (!$printer_info = $this->field('id,machine_code,msign')->find($id)) {
                $this->error = '打印机不存在';
                return false;
            }
            $this->startTrans();
            if (!$this->delete($id)) {
                $this->rollback();
                $this->error = $this->error ? : '删除失败';
                return false;
            } else {

                // 远程终端删除
                $OrderPrintServer = new OrderPrintServer;
                $result = $OrderPrintServer->del_printer($printer_info['machine_code'], $printer_info['msign']);

                if (1 != $result['status']) {
                    $this->rollback();
                    $this->error = $result['info'];
                    return false;
                }

                // 打印机与商店关联关系删除
                $shop_printer = D('ShopPrinter');
                if (!$shop_printer->printer_extend_del($id)) {
                    $this->rollback();
                    $this->error = $shop_printer->error ? : '解除与商店关联关系失败';
                    return false;
                }

                $this->commit();
                return true;
            }
        }
    }
}
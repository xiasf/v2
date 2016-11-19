<?php
/**
 * S模块 订单模型
 * Update time：2016-5-8 23:58:34
 */

namespace Seller\Model;
use Core\Model;
use Seller\Server\OrderPrintServer;

class OrderModel extends Model {

    protected $_auto = array(
        array('update_time', NOW_TIME, self::MODEL_UPDATE),
        array('version', array('exp', 'version+1'), self::MODEL_UPDATE),
    );

    // 分单
    // * 已经成功分单的订单不能再次分单！
    // * 只有平台商家才可以接受分单！
    // * 只有平台商家才可以分单！
    // * 在线支付订单必须支付成功后才能操作！
    // * 分单必须在同一类目下！
    public function final_shop_order($final_shop_id, $order_id) {
        try {
            if (!$final_shop_info = M('shop')->find($final_shop_id)) {
                throw new \Exception('商家不存在！');
            }
            if (1 != $final_shop_info['shop_business_type']) {
                throw new \Exception('只有平台商家才可以接受分单！');
            }
            $this->startTrans();
            if (!$order_info = $this->lock(true)->find($order_id)) {
                throw new \Exception('订单不存在！');
            }
            if (0 != $order_info['final_shop_id']) {
                throw new \Exception('您的订单已经分单，不能再次分单！');
            }
            if (2 == $order_info['pay_type'] && 1 != $order_info['pay_status']) {
                throw new \Exception('在线支付订单必须支付成功后才能操作！');
            }
            $shop_info = M('shop')->find($order_info['shop_id']);
            if (1 != $shop_info['shop_business_type']) {
                throw new \Exception('只有平台商家才可以分单！');
            }
            if ($final_shop_info['shop_type'] != $shop_info['shop_type']) {
                throw new \Exception('分单必须在同一类目下！');
            }
            if (!$this->create(array('id' => $order_id, 'final_shop_id' => $final_shop_id)) || !$this->save()) {
                throw new \Exception('分单失败！');
            }
            if (!$this->add_order_log($order_id, '您的订单被分单到店铺【' . $final_shop_info['shop_name'] . '(' . $final_shop_id . ')】', UID, 1, NOW_TIME)) {
                throw new \Exception('日志写入失败！');
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }


    // 确认订单
    // * 只有待确认的原始订单才能进行确认操作！
    // * 在线支付订单必须支付成功后才能操作！
    // * 只有待确认的订单才能进行确认操作（被确认了的订单不能再取消哦，所以请慎重操作）
    public function sure_order($order_id) {
        try {
            $this->startTrans();
            if (!$order_info = $this->lock(true)->find($order_id)) {
                throw new \Exception('订单不存在！');
            }
            if (0 != $order_info['status']) {
                throw new \Exception('只有待确认的原始订单才能进行确认操作！');
            }
            if (2 == $order_info['pay_type'] && 1 != $order_info['pay_status']) {
                throw new \Exception('在线支付订单必须支付成功后才能操作！');
            }
            if (!$this->create(array('id' => $order_id, 'status' => 1)) || !$this->save()) {
                throw new \Exception('确认订单失败！');
            }
            if (!$this->add_order_log($order_id, '您的订单被商家确认！操作员：' . UID, UID, 1, NOW_TIME)) {
                throw new \Exception('日志写入失败！');
            }
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            $this->error = $e->getMessage();
            return false;
        }
    }


    // 取消订单
    // * 只有待确认的原始订单和已经确认的订单才能进行取消操作！
    // * 支付成功的订单不能执行取消操作！
    // * 只有待确认的订单才能被取消（被取消了的订单不能再被确认哦，所以请慎重操作）
    public function cancel_order($order_id) {
        $this->startTrans();
        if (!$order_info = $this->lock(true)->find($order_id)) {
            $this->error = '订单不存在！';
            $this->rollback();
            return false;
        }
        if (1 < $order_info['status']) {   // 只有待确认的订单才能被取消
            $this->error = '只有待确认的原始订单和已经确认的订单才能进行取消操作！';
            $this->rollback();
            return false;
        }
        if (2 == $order_info['pay_type'] && 1 == $order_info['pay_status']) {
            $this->error = '支付成功的订单不能执行取消操作！';
            $this->rollback();
            return false;
        }
        if (!$this->create(array('id' => $order_id, 'status' => 3)) || !$this->save()) {
            $this->error = '取消订单失败！';
            $this->rollback();
            return false;
        }
        $this->add_order_log($order_id, '您的订单被商家取消！操作员：' . UID, UID, 1, NOW_TIME);
        if ($this->error || $this->getDbError()) {
            $this->rollback();
            $this->error = $this->error ? : '系统错误';
            return false;
        }
        $this->commit();
        return true;
    }


    // 推送打印订单
    // * 在线支付订单必须支付成功后才能打印！
    public function send_print_order($order_id, $printer_id = 0) {
        $this->startTrans();
        if (!$order_info = $this->lock(true)->find($order_id)) {
            $this->error = '订单不存在！';
            $this->rollback();
            return false;
        }
        if (2 == $order_info['pay_type'] && 1 != $order_info['pay_status']) {
            $this->error = '在线支付订单必须支付成功后才能打印！';
            $this->rollback();
            return false;
        }

        $shop_id = $order_info['final_shop_id'] ? : $order_info['shop_id']; // 未分单的订单也可以手动打印，如果已分单则以分单为主

        if (!$shop_info = M('shop')->field('shop_name')->find($shop_id)) {
            $this->error = '商家不存在！';
            $this->rollback();
            return false;
        }

        // 获取打印机
        if ($printer_id) {
            $printer_info = M('printer')->where(array('id' => $printer_id))->find();
        } else {
            $printer_info = $this->get_shop_printer_rand($shop_id);
        }

        if (empty($printer_info['id'])) {
            $this->error = '店铺还没有绑定打印机！';
            $this->rollback();
            return false;
        }

        // 附加订单item
        $order_info['item'] = M('order_item')->where(array('order_id' => $order_id))->select();

        // 附加第几张票
        $order_info['serial'] = M('order_paper')->where(array('order_id' => $order_id))->count() + 1;

        // 附加第几张票
        $order_info['c2'] = $this->where(array('shop_id' => $order_info['shop_id']))->count() + 1;

        // 获取打印内容
        $content = $this->_get_order_print_content($order_info);

        $this->add_order_log($order_id, '您的订单正在推送到商店【'.$shop_info['shop_name'].'('.$shop_id.')】打印机【' . $printer_info['printname'] . '(' . $printer_info['id'] . ')】', 2, UID, NOW_TIME);
        // 推送到打印机
        $OrderPrintServer = new OrderPrintServer;
        $result = $OrderPrintServer->print_send($content, $printer_info['machine_code'], $printer_info['msign']);
        $time = time();
        if ($result['status'] == 1) {
            $data = array(
                'order_id' => $order_id,
                'paper_id' => $result['id'],
                'serial' => $order_info['serial'],
                'printer_id' => $printer_info['id'],
                'create_time' => $time,
            );
            $order_paper = M('order_paper');
            $order_paper->add($data);                                                             // 生成票据
            $this->save(array('id' => $order_id, 'print_status' => 1, 'print_time' => $time, 'update_time' => $time));   // 更新订单打印状态

            // 日志必须实时同步，流程必须有空间层次感

            // 创建订单日志
            $this->add_order_log($order_id, '您的订单推送到【'.$shop_info['shop_name'].'('.$shop_id.')】打印机【' . $printer_info['printname'] . '(' . $printer_info['id'] . ')】成功，票据：' . $result['id'], 2, UID, $time);
            // $this->add_order_log($order_id, '订单推送成功，成功生成票据：' . $result['id'], 2, UID, $time);

            // 创建票据日志
            $this->add_order_paper_log($result['id'], '生成票据成功！', 0, $time);

        } else {
            // 推送失败
            $this->save(array('id' => $order_id, 'print_status' => 2, 'update_time' => $time));   // 更新订单打印状态
            $this->add_order_log($order_id, '您的订单推送到【'.$shop_info['shop_name'].'('.$shop_id.')】打印机【' . $printer_info['printname'] . '(' . $printer_info['id'] . ')】失败', 1, UID, $time);
            $error = $result['info'];
        }

        if ($this->error || $this->getDbError()) {
            $this->rollback();
            $this->error = $this->error ? : '系统错误';
            return false;
        }
        $this->commit();

        if (isset($error)) {
            $this->error = $error;
            return false;
        }
        return true;
    }


    // 随机获取某商店绑定的一台打印机
    // * 优化成取得不是很繁忙的那台（根据每台打印机在票据表未打印的数量）
    public function get_shop_printer_rand($shop_id) {
        $shop = M('shop s');
        $result = $shop
        ->join('INNER JOIN __SHOP_PRINTER__ sp ON sp.shop_id = s.id')
        ->join('INNER JOIN __PRINTER__ p ON sp.printer_id = p.id')
        ->join('LEFT JOIN __ORDER_PAPER__ op ON op.printer_id = p.id and op.print_status = 0')
        ->field('p.*,count(op.printer_id) as vv')
        ->where(array('s.id' => $shop_id))
        ->order('vv asc')
        ->find();
        return $result;
    }


    // 添加一条订单日志（记录订单操作，状态重要日志，一些错误信息不在日志记录范畴）
    public function add_order_log($order_id, $note, $mid, $uid, $time) {
        $log = array(
            'order_id' => $order_id,
            'uid' => $uid,
            'note' => $note,
            'create_time' => $time,
            'mid' => $mid,
        );
        return M('order_log')->add($log);
    }


    // 添加一条订单票据日志（记录票据生成，打印状态更新日志）
    public function add_order_paper_log($paper_id, $note, $mid, $time) {
        $log = array(
            'paper_id' => $paper_id,
            'note' => $note,
            'create_time' => $time,
            'mid' => $mid,
        );
        return M('order_paper_log')->add($log);
    }


    // 获取一条订单打印内容（可做成模板）
    private function _get_order_print_content($order_info) {
        if ($order_info['pay_type'] == 1) {
            $pay_type  = '餐到付款';
        } else {
            $msg = json_decode($order_info['webhook_json']);
            $pay_type = array('ALI' => '支付宝', 'WX' => '微信支付', 'UN' => '银行卡');
            $pay_type = '在线支付【已支付】\n支付渠道:' . $pay_type[$msg->channelType];
        }
        $content = '@@2【' . $order_info['order_name'] . '】' .
                    '\n============================' . 
                    '\n票序号:' . $order_info['serial'] . 
                    '\n店铺今日第几单:' . $order_info['c2'] . 
                    '\n订单号:' . $order_info['id'] . 
                    '\n============================' . 
                    '\n菜名           数量     金额\n';
        foreach ($order_info['item'] as $value) {
            $value['product_unit'] = $value['product_unit'] ? : '份';
            $content .= iconv_substr($value['product_name'] . str_repeat('　', 10), 0, 7, 'utf-8') . '　' . iconv_substr($value['product_num'] . $value['product_unit'] . str_repeat(' ', 10), 0, 7, 'utf-8') . '￥' . $value['summary'] . '\n';
        }
        // if ($order_info['pei']) $content .= '\n配送费         ---       ' . $order_info['pei'] . '元';
        $content .= 
                '----------------------------'.
                '\n合计:   ' . $order_info['total'] . '元' . 
                '\n----------------------------' . 
                '\n下单时间:' . $order_info['create_time'] . 
                '\n----------------------------' . 
                // '\n送达:' . $order_info['s_t'].
                '\n电话:'.$order_info['mobile'] . '  姓名:' . ($order_info['user_name'] ? : '未填写') . 
                '\n地址:' . $order_info['location'] . ' ' . $order_info['address'] . 
                '\n备注:' . ($order_info['note'] ? $order_info['note'] : '无') . 
                '\n支付方式:' . $pay_type . 
                '\n----------------------------' . 
                '\n↑↑微信扫一扫，惊喜不断↑↑' .
                '\nhbdscy.com 德胜餐饮，德烩配味。' .
                '\n<b>18583136212</b>' .
                '\n<q>http://www.飞地方法规飞</q>'



                ;
        return $content;
    }
}
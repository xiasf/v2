<?php
/**
 * 分店模型
 * Update time：2016-4-28 09:28:42
 */

namespace Seller\Model;
use Seller\Model\CommonModel;
use Common\Util\BaiduMapLbs;

class ShopModel extends CommonModel {

    protected $insertFields = 'shop_name,seller_id,describe,contacts_mobile,contacts_email,contacts_name,business_status,status,logo,address,sort,lng,lat,shop_business_type,shop_type,address_reference,settlement_account,bank_name,bank_user_name,bank_card,is_seller_settlement,store_bitmap';
    protected $updateFields = 'id,shop_name,describe,contacts_mobile,contacts_email,contacts_name,business_status,logo,address,sort,lng,lat,address_reference,settlement_account,bank_name,bank_user_name,bank_card,is_seller_settlement,store_bitmap';

    protected $_validate = array(
        array('shop_name', '1,15', '分店名称不合法', self::EXISTS_VALIDATE, 'length'),
        array('describe', '1,100', '描述不合法', self::EXISTS_VALIDATE, 'length'),
        array('contacts_mobile', 'mobile', '手机号码不合法', self::EXISTS_VALIDATE),
        array('contacts_email', 'email', '邮箱不合法', self::EXISTS_VALIDATE),
        array('shop_type', '1,2,3,4', '商店分类不合法', self::EXISTS_VALIDATE, 'in'),
        array('print_mode', '1,2', '商店打印模式不合法', self::EXISTS_VALIDATE, 'in'),
        array('is_seller_settlement', '0,1', '提款账号设置不合法', self::EXISTS_VALIDATE, 'in'),
        array('status', '0,1', '状态不合法', self::EXISTS_VALIDATE, 'in'),
        array('business_status', '0,1', '营业状态不合法', self::EXISTS_VALIDATE, 'in'),
        array('logo', 'check_img', '图片不合法', self::EXISTS_VALIDATE, 'callback'),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_UPDATE),
        array('status', 0, self::MODEL_INSERT),
        array('shop_business_type', 2, self::MODEL_INSERT),
        array('seller_id', 'session', self::MODEL_INSERT, 'function', 'user_auth.seller_id'),
        array('business_status', 'get_status', self::MODEL_BOTH, 'callback'),
    );


    // 设置商店的打印模式
    public function set_print_mode($shop_id, $print_mode) {
        if (IS_POST) {
            if (!$this->field('id')->find($shop_id)) {
                $this->error = '分店不存在';
                return false;
            }
            if (!$this->field('shop_id,print_mode')->create() || false === $this->save()) {
                $this->error = '设置失败！';
                return false;
            }
            return true;
        }
    }


    public function shop_add() {
        if (IS_POST) {
            $this->startTrans();

            if (!($data = $this->create()) || !$new_id = $this->add()) {
                $this->rollback();
                $this->error = $this->error ? : '添加失败';
                return false;
            } else {
                $poi_data = array(
                    'title' => $data['shop_name'],
                    'longitude' => $data['lng'],
                    'latitude' => $data['lat'],
                    'shop_id' => $new_id,
                    'tags' => get_shop_type($data['shop_type']), // 店铺类型作为标签
                    'shop_type' => $data['shop_type'],
                    'shop_business_type' => $data['shop_business_type'],
                    'address' => $data['address'],
                    'address_reference' => $data['address_reference'],
                );
                $result = $this->create_poi($poi_data);
                $result = json_decode($result, true);
                if ($result['status'] != 0) {
                    $this->rollback();
                    $this->error = $result['message'];
                    return false;
                }
                $this->commit();
                return true;
            }
        }
    }


    public function shop_update() {
        if (IS_POST) {
            if (!$this->field('id')->find(I('post.id/d'))) {
                $this->error = '分店不存在';
                return false;
            }
            $this->startTrans();
            if (!($data = $this->create()) || false === $this->save()) {
                $this->rollback();
                $this->error = $this->error ? : '更新失败';
                return false;
            } else {
                $poi_data = array(
                    'title' => $data['shop_name'],
                    'longitude' => $data['lng'],
                    'latitude' => $data['lat'],
                    'shop_id' => $data['id'],
                    'tags' => get_shop_type($data['shop_type']), // 店铺类型作为标签
                    'shop_type' => $data['shop_type'],
                    'shop_business_type' => $data['shop_business_type'],
                    'address' => $data['address'],
                    'address_reference' => $data['address_reference'],
                );
                $poi = $this->so_poi(array('shop_id' => $poi_data['shop_id']));
                $poi = json_decode($poi, true);
                if ($poi['status'] == 0 && $poi['total']) { // 更新
                    unset($poi_data['shop_id']);
                    $poi_data['id'] = $poi['pois'][0]['id'];
                    $result = $this->up_poi($poi_data);
                } else {
                    // 创建
                    $result = $this->create_poi($poi_data);
                }
                $result = json_decode($result, true);
                if ($result['status'] != 0) {
                    $this->rollback();
                    $this->error = $result['message'];
                    return false;
                }
                $this->commit();
                return true;
            }
        }
    }


    // 非物理删除
    public function shop_delete($id) {
        if (IS_POST) {
            if (!$this->field('id')->find($id)) {
                $this->error = '分店不存在';
                return false;
            }
            $this->startTrans();
            if (false === $this->update(array('id' => $id, 'is_del' => 1))) {
                $this->rollback();
                $this->error = $this->error ? : '删除失败';
                return false;
            } else {
                $this->commit();
                return true;
            }
        }
    }


    // 彻底物理删除
    public function true_shop_delete($id) {
        if (IS_POST) {
            if (!$this->field('id')->find($id)) {
                $this->error = '分店不存在';
                return false;
            }
            $this->startTrans();
            if (!$this->delete($id)) {
                $this->rollback();
                $this->error = $this->error ? : '删除失败';
                return false;
            } else {

                // 删除远程地图数据
                $result = $this->del_poi(array('key' => $id));
                $result = json_decode($result, true);
                if (0 != $result['status']) {
                    $this->rollback();
                    $this->error = $result['message'];
                    return false;
                }

                // 打印机与商店关联关系删除
                $shop_printer = D('ShopPrinter');
                if (!$shop_printer->shop_extend_del($id)) {
                    $this->rollback();
                    $this->error = $shop_printer->error ? : '解除与打印机关联关系失败';
                    return false;
                }

                $this->commit();
                return true;
            }
        }
    }

    // 创建poi
    private function create_poi($data) {
        $BaiduMapLbs = new BaiduMapLbs;
        $result = $BaiduMapLbs->create_poi($data);
        return $result;
    }

    // 根据条件删除poi
    private function del_poi($where) {
        $BaiduMapLbs = new BaiduMapLbs;
        $result = $BaiduMapLbs->del_poi($where);
        return $result;
    }

    // 根据条件查询poi
    public function so_poi($where) {
        $BaiduMapLbs = new BaiduMapLbs;
        $result = $BaiduMapLbs->so_poi($where);
        return $result;
    }

    // 更新poi
    private function up_poi($data) {
        $BaiduMapLbs = new BaiduMapLbs;
        $result = $BaiduMapLbs->up_poi($data);
        return $result;
    }
}
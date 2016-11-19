<?php
/**
 * 商户模型
 * Update time：2016-6-4 01:41:24
 */

namespace S\Model;
use S\Model\CommonModel;
use Common\UserServer\Api\UserApi as UserApi;

class SellerModel extends CommonModel {
    protected $insertFields = 'uid,seller_name,logo,company_name,describe,settlement_account,bank_name,bank_user_name,bank_card,cash,tax,contacts_name,contacts_mobile,contacts_email,contacts_phone,paper_img,create_ip,create_time,status';
    protected $updateFields = 'id,seller_name,logo,company_name,describe,settlement_account,bank_name,bank_user_name,bank_card,cash,tax,contacts_name,contacts_mobile,contacts_email,contacts_phone,paper_img,update_time,status';

    /* 用户模型自动完成 */
    protected $_auto = array(
        array('login', 0, self::MODEL_INSERT),
        array('create_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('last_login_ip', 0, self::MODEL_INSERT),
        array('last_login_time', 0, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_UPDATE),
        array('status', 'get_status', self::MODEL_BOTH, 'callback'),
    );

    protected $_validate = array(
        array('seller_name', '1,15', '商户名称不合法', self::EXISTS_VALIDATE, 'length'),
        array('seller_name', '', '商户名称被占用', self::EXISTS_VALIDATE, 'unique'), //用户名被占用
        array('contacts_mobile', 'mobile', '手机号码不合法', self::EXISTS_VALIDATE),
        array('contacts_email', 'email', '邮箱不合法', self::EXISTS_VALIDATE),
        array('logo', 'check_img', '图片不合法', self::EXISTS_VALIDATE, 'callback'),
        array('paper_img', 'check_img', '图片不合法', self::EXISTS_VALIDATE, 'callback'),
    );


    /**
     * 在当前应用中注册用户
     * @param  integer $uid 用户ID
     * @return mixed   false-注册失败
     */
    public function register() {
        // 账户登录信息
        $data = array(
            'username' => I('post.username/s'),
            'email'    => I('post.email/s'),
            'mobile'   => I('post.mobile/s'),
            'password' => I('post.password/s'),
            'confirm_password' => I('post.confirm_password/s'),
        );
        if ($data['confirm_password'] != $data['password']) {
            $this->error = '两次密码输入不一致！';
            return false;
        }
        // 注册账号
        $User = new UserApi;
        $User->startTrans();
        $uid = $User->register($data);
        if (0 < $uid) {
            // 账号注册成功，创建商户
            $seller = $this->create();
            if (!$seller) {
                $User->rollback();
                $this->rollback();
                return false;
            }
            $seller['uid'] = $uid;
            $this->startTrans();
            if (!$this->add($seller)) {
                $User->rollback();
                $this->rollback();
                $this->error = $this->error ? : '商户创建失败，请重试！';
                return false;
            }
        } else { // 注册失败，显示错误信息
            $User->rollback();
            $this->error = $User->showRegError($uid);
            return false;
        }
        $User->commit();
        $this->commit();
        return true;
    }


    public function seller_update() {
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
                $this->commit();
                return true;
            }
        }
    }
}
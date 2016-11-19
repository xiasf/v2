<?php

namespace Common\Model;
use Core\Model;
use Common\Server\User\Api\UserApi as UserApi;

/**
 * 商户登陆模型
 * Update time：2016-6-6 14:59:00
 */

class SellerModel extends Model {

    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid) {
        if (!$user = $this->field(true)->where(array('uid' => $uid))->find()) {
            $this->error = '商户不存在！';
            return false;
        } elseif (1 != $user['status']) {
            $this->error = '商户未激活或已禁用！'; // 应用级别禁用
            return false;
        }
        /* 登录用户 */
        $this->autoLogin($user);
        return true;
    }


    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoLogin($user) {
        /* 更新登录信息 */
        $data = array(
            'uid'             => $user['uid'],
            'login'           => array('exp', '`login`+1'),
            'last_login_time' => NOW_TIME,
            'last_login_ip'   => get_client_ip(1),
        );
        $this->save($data);

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'             => $user['uid'],
            'seller_id'       => $user['id'],
            'seller_name'        => $user['seller_name'],
            'last_login_time' => NOW_TIME,
        );

        session('user_auth', $auth);
        session('user_auth_sign', data_auth_sign($auth));
    }


    /**
     * 注销当前用户
     * @return void
     */
    public function logout() {
        session('user_auth', null);
        session('user_auth_sign', null);
    }
}
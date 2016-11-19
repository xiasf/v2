<?php

namespace Common\Model;
use Core\Model;
use Common\Server\User\Api\UserApi as UserApi;

/**
 * 用户基础模型
 * Update time：2016-6-6 14:59:00
 */

class MemberModel extends Model {

    /* 用户模型自动完成 */
    protected $_auto = array(
        array('login', 0, self::MODEL_INSERT),
        array('create_ip', 'get_client_ip', self::MODEL_INSERT, 'function', 1),
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        // array('update_time', NOW_TIME, self::MODEL_UPDATE),     // 登录是不会改变这个的
        array('status', 1, self::MODEL_INSERT),
    );

    /**
     * 登录指定用户
     * @param  integer $uid 用户ID
     * @return boolean      ture-登录成功，false-登录失败
     */
    public function login($uid) {
        if (!$user = $this->register($uid)) {
            return false;
        } elseif (1 != $user['status']) {
            $this->error = '用户未激活或已禁用！'; //应用级别禁用
            return false;
        }
        /* 登录用户 */
        $this->autoLogin($user);
        return true;
    }


    /**
     * 在当前应用中注册用户
     * @param  integer $uid 用户ID
     * @return mixed   false-注册失败
     */
    public function register($uid) {
        /* 检测是否在当前应用注册 */
        $user = $this->field(true)->find($uid);
        if (!$user) { //未注册
            /* 在当前应用中注册用户 */
            $Api = new UserApi();
            $info = $Api->info($uid);

            $nickname = $info[1] ? : ($info[3] ? : ($info[2] ? : ''));

            $user = $this->create(array('uid' => $uid, 'nickname' => $nickname, 'status' => 1), 1);
            if (!$this->add($user)) {
                $this->error = '前台用户信息注册失败，请重试！';
                return false;
            }
        }
        return $user;
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
            'username'        => $user['nickname'],
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
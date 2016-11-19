<?php

namespace Common\Model;
use Common\Util\WechatOauth;

/**
 * Oauth模型
 * Update time：2016-6-24 01:50:17
 */

class OauthAccountModel {

    /* 用户模型自动完成 */
    protected $_auto = array(
        // array('create_time', NOW_TIME, self::MODEL_INSERT),
    );


    // 根据uid获取绑定的openid
    public function getOpenidForUid($uid, $unionid, $oauth_id)
    {
        $openid = M('ucenter_member u')
              ->join('__OAUTH_USER__ ou ON ou.uid = u.id')
              ->field('ou.openid')
              ->where(array('ou.oauth_id' => $oauth_id, 'u.id' => $uid))
              ->find();
        return $openid ? $openid['openid'] : null;
    }


    // 根据openid获取绑定的uid
    public function getUidForOpenid($openid, $unionid, $oauth_id)
    {
        $uid = M('ucenter_member u')
              ->join('__OAUTH_USER__ ou ON ou.uid = u.id')
              ->field('u.id')
              ->where(array('ou.oauth_id' => $oauth_id, 'ou.openid' => $openid))
              ->find();
        return $uid ? $uid['id'] : null;
    }


    public function check_openid($uid, $openid, $unionid, $oauth_id)
    {
        // 如果有UnionID，那么则用UnionID机制判断是否绑定过账号（不然会出现两个公众号有两个账号）
        // 绑定时还是用openid，处理好UnionID机制的关系即可

        // 1：检测openid是否有效，这个可以跳过不用检测了

        if ($uid_ = $this->getUidForOpenid($openid, $unionid, $oauth_id)) {
            if ($uid == $uid_) {
                return -2;
            } else {
                return -1;
            }
        } else {
            return 1;
        }
    }


    public function check_user($uid, $openid, $unionid, $oauth_id)
    {
        if ($openid_ = $this->getOpenidForUid($uid, $unionid, $oauth_id)) {
            if ($openid == $openid_) {
                return -2;
            } else {
                return -1;
            }
        } else {
            return 1;
        }
    }


    // 执行账号绑定
    public function bindAccount($uid, $openid, $unionid, $oauth_id)
    {
        $oauth_id = session('oauth_id');

        $result = array('status' => 1, 'info' => '账号绑定成功');
        // 检测
        $checkInfo = $this->checkOauthAccount($uid, $openid, $unionid, $oauth_id);
        if (0 == $checkInfo['status']) {
            $result = array('status' => 0, 'info' => $checkInfo['info']);
            return $result;
        }

        // 执行绑定操作
        $data = array(
            'uid' => $uid,
            'openid' => $openid,
            'oauth_id' => $oauth_id,
            'create_time' => NOW_TIME,
        );
        if (M('oauth_user')->add($data)) {
            return $result;
        } else {
            return array('status' => 0, 'info' => '账号绑定失败');
        }
    }


    // 检测账号第三方绑定情况
    private function checkOauthAccount($uid, $openid, $unionid = null, $oauth_id)
    {   
        $result = ['status' => 1, 'info' => '可以绑定'];

        $wechatOauth = new WechatOauth;
        $check_result = $wechatOauth->checkToken(session('oauth.access_token'), $openid);
        if (true !== $check_result) {
            return ['status' => 0, 'info' => '[' . $check_result['errcode'] . ']' . $check_result['errmsg']];
        }

        if (!$e = $this->check_openid($uid, $openid, $unionid, $oauth_id)) {
            switch($e) {
                case -1: $error = 'openid已绑定其他账号'; break;   // 如果上一步正确的话会自动登录的
                case -2: $error = 'openid已绑定此用户，不能重复绑定'; break;
                default: $error = 'openid不能绑定，未知错误！'; break;
            }
            $result['status'] = 0;
            $result['info'] = $error;
            return $result;
        }

        if (!$e = $this->check_user($uid, $openid, $unionid, $oauth_id)) {
            switch($e) {
                case -1: $error = '用户已绑定其他社交账号，请先登录解绑'; break;
                case -2: $error = '用户已绑定该社交账号，不能重复绑定'; break;
                default: $error = 'uid不能绑定，未知错误！'; break;
            }
            $result['status'] = 0;
            $result['info'] = $error;
            return $result;
        }
        return $result;
    }


    // 记录v2_oauth_token
    public function addOauthToken()  {

    }


    // 关联 unionid
    public function associatedUnionid() {

    }


    // 解绑

}
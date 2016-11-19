<?php
/**
 * oauth模型
 * Update time：2016-6-14 16:01:45
 */

namespace S\Model;
use S\Server\OrderPrintServer;

class OauthModel extends CommonModel {

    protected $insertFields = 'title,name,description,config,logo,status,create_time';
    protected $updateFields = 'id,title,name,description,config,logo,status,create_time';

    protected $_validate = array(
        array('title', 'require', '不能为空', self::EXISTS_VALIDATE),
        array('name', 'require', '不能为空', self::EXISTS_VALIDATE),
        array('AppID', 'require', '不能为空', self::EXISTS_VALIDATE),
        array('AppSecret', 'require', '不能为空', self::EXISTS_VALIDATE),
        // array('logo', 'check_img', '图片不合法', self::EXISTS_VALIDATE, 'callback'),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_UPDATE),
        array('status', 'get_status', self::MODEL_BOTH, 'callback'),
        array('config', 'create_config', self::MODEL_BOTH, 'callback'),
    );


    protected function create_config() {
        return serialize(array('AppID' => $_POST['AppID'], 'AppSecret' => $_POST['AppSecret']));
    }


    public function oauth_add() {
        if (IS_POST) {
            $this->startTrans();
            if ((!$data = $this->create()) || !$this->add()) {
                $this->rollback();
                $this->error = $this->error ? : '添加失败';
                return false;
            } else {
                $this->commit();
                return true;
            }
        }
    }


    public function oauth_update() {
        if (IS_POST) {
            if (!$this->field('id')->find(I('post.id/d'))) {
                $this->error = 'oauth不存在';
                return false;
            }
            $this->startTrans();
            if (!$this->create() || false === $this->save()) {
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
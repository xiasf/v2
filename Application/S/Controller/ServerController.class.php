<?php
/**
 * Server管理控制器
 * Update time：2016-3-6 17:40:55
 */

namespace S\Controller;
use Core\Page;

class ServerController extends BaseController {

	public function oauth_list() {
        $oauth = M('oauth');
        $this->oauth_list = $oauth->page(I('get.page/d'), 10)->order('id desc')->select();
        $this->oauth_count = $oauth->count('id');
        $page = new Page($this->oauth_count, 10);
        $this->page_show = $page->show();
		$this->display();
	}


	public function oauth_add() {
        if (IS_POST) {
        	$oauth = D('oauth');
        	if (!$oauth->oauth_add()) {
                $this->error($oauth->getError());
        	}
            $this->success('添加成功！', U('oauth_list'));
        } else {
            $this->display();
        }
    }


    public function oauth_update() {
        $oauth = D('oauth');
        if (IS_POST) {
            if (!$oauth->oauth_update()) {
                $this->error($oauth->getError());
        	}
            $this->success('更新成功！', U('oauth_list'));
        } else {
            $id = I('get.id/d');
            if (!$oauth_data = $oauth->field(true)->find($id))
                $this->error('分店不存在！');
            $oauth_config = unserialize($oauth_data['config']);
            $oauth_data['AppID'] = $oauth_config['AppID'];
            $oauth_data['AppSecret'] = $oauth_config['AppSecret'];
            $this->oauth_data = $oauth_data;
            $this->display();
        }
    }


    // 非物理删除
    public function oauth_delete() {
        if (IS_POST) {
            $oauth = D('oauth');
            if (!$oauth->oauth_delete(I('post.id/d'))) {
                $this->error($oauth->getError());
            }
            $this->success('删除成功！', U('oauth_list'));
        }
    }


    // 彻底物理删除
    public function oauth_trur_delete() {
        if (IS_POST) {
            $oauth = D('oauth');
        	if (!$oauth->true_oauth_delete(I('post.id/d'))) {
            	$this->error($oauth->getError());
        	}
            $this->success('彻底删除成功！', U('oauth_list'));
        }
    }
}
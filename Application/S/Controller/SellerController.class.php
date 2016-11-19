<?php
/**
 * 商户管理控制器
 * Update time：2016-3-6 17:40:55
 */

namespace S\Controller;
use Core\Page;

class SellerController extends BaseController {

    // 获取商户
    public function get_seller() {
        $map['seller_name'] = array('like', '%' . I('get.name/s') . '%');
        $seller_list = M('seller')->where($map)->page(I('get.page/d'), 10)->select();
        $this->ajaxReturn($seller_list);
    }

	public function seller_list() {
        $seller = M('seller');
        $this->seller_list = $seller->page(I('get.page/d'), 10)->order('id desc')->select();
        $this->seller_count = $seller->count('id');
        $page = new Page($this->seller_count, 10);
        $this->page_show = $page->show();
		$this->display();
	}


    /* 注册商户 */
    public function add() {
        if (IS_POST) {
            $Seller = D('Seller');
            if (!$Seller->register()) {
                $this->error($Seller->getError());
            } else {
                $this->success('创建商户成功！', U('seller_list'));
            }
        } else {
            $this->display();
        }
    }


    // 编辑商户（不编辑账号信息）
    public function update() {
        $Seller = D('Seller');
        if (IS_POST) {
            if (!$Seller->seller_update()) {
                $this->error($Seller->getError());
            }
            $this->success('更新成功！', U('seller_list'));
        } else {
            $id = I('get.id/d');
            if (!$this->seller_data = $Seller->field(true)->find($id))
                $this->error('分店不存在！');
            $this->display();
        }
    }
}
<?php
/**
 * 云餐品管理控制器
 * Update time：2016-3-6 17:40:55
 */

namespace S\Controller;
use Core\Page;

class CloudProductsController extends BaseController {

	public function products_list() {
        $products = M('products');
        $products_list = $products->page(I('get.page/d'), 10)->where(array('shop_id' => 0))->order('id desc')->select();

        $cloud_products_category_extend = M('cloud_products_category_extend');
        $cloud_products_category = M('cloud_products_category');
        foreach ($products_list as &$value) {
            $category_extend = $cloud_products_category_extend->where(array('products_id' => $value['id']))->find();
            if ($category_extend) {
                $category_info = $cloud_products_category->find($category_extend['category_id']);
                $value['category_id'] = $category_info['id'];
                $value['category_name'] = $category_info['name'];
            } else {
                $value['category_id'] = '';
                $value['category_name'] = '';
            }
        }

        $this->products_list = $products_list;

        $this->products_count = $products->count('id');
        $page = new Page($this->products_count, 10);
        $this->page_show = $page->show();
		$this->display();
	}


	public function add() {
        if (IS_POST) {
        	$products = D('CloudProducts');
        	if (!$products->products_add()) {
                $this->error($products->getError());
        	}
            $this->success('添加成功！', U('products_list'));
        } else {
            $this->cloud_products_category = $this->get_cloud_products_category();
            $this->display();
        }
    }


    public function update() {
        $products = D('CloudProducts');
        if (IS_POST) {
            if (!$products->products_update()) {
                $this->error($products->getError());
        	}
            $this->success('更新成功！', U('products_list'));
        } else {
            $id = I('get.id/d');
            if (!$products_data = $products->field(true)->find($id)) {
                $this->error('餐品不存在！');
            }
            $cloud_products_category_extend = M('cloud_products_category_extend');
            $category_extend = $cloud_products_category_extend->where(array('products_id' => $id))->select();
            $products_data['cloud_category_id'] = $category_extend[0]['category_id'];
            $this->products_data = $products_data;
            $this->cloud_products_category = $this->get_cloud_products_category();
            $this->display();
        }
    }


    // 非物理删除
    public function delete() {
        if (IS_POST) {
            $products = D('CloudProducts');
            if (!$products->products_delete(I('post.id/d'))) {
                $this->error($products->getError());
            }
            $this->success('删除成功！', U('products_list'));
        }
    }


    // 彻底物理删除
    public function trur_delete() {
        if (IS_POST) {
            $products = D('CloudProducts');
        	if (!$products->trur_products_delete(I('post.id/d'))) {
            	$this->error($products->getError());
        	}
            $this->success('彻底删除成功！', U('products_list'));
        }
    }


    // 获取云分类
    public function get_cloud_products_category() {
        $cloud_products_category = M('cloud_products_category');
        $a = $cloud_products_category->select();

        $tem = array();
        foreach ($a as $value) {
            if (!isset($tem[$value['shop_type']])) {
                $tem[$value['shop_type']] = array();
            }
            $tem[$value['shop_type']][] = $value;
        }
       return $tem;
    }
}
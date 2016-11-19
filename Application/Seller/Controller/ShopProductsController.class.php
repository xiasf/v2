<?php
/**
 * 商店餐品管理控制器
 * Update time：2016-3-6 17:40:55
 */
namespace Seller\Controller;
use Core\Page;

class ShopProductsController extends BaseController {

	public function products_list() {
        $products = M('products');
        $where = array('shop_id' => array('neq', 0));
        $products_list = $products->page(I('get.page/d'), 10)->where($where)->order('id desc')->select();

        $shop_products_category_extend = M('shop_products_category_extend');
        $shop_products_category = M('shop_products_category');
        foreach ($products_list as &$value) {
            $category_extend = $shop_products_category_extend->where(array('products_id' => $value['id']))->find();
            if ($category_extend) {
                $category_info = $shop_products_category->find($category_extend['category_id']);
                $value['category_id'] = $category_info['id'];
                $value['category_name'] = $category_info['name'];
            } else {
                $value['category_id'] = '';
                $value['category_name'] = '';
            }
        }

        $this->products_list = $products_list;

        $this->products_count = $products->where($where)->count('id');
        $page = new Page($this->products_count, 10);
        $this->page_show = $page->show();
		$this->display();
	}


	public function add() {
        if (IS_POST) {
        	$products = D('ShopProducts');
        	if (!$products->products_add()) {
                $this->error($products->getError());
        	}
            $this->success('添加成功！', U('products_list'));
        } else {
            $this->display();
        }
    }


    public function update() {
        $products = D('ShopProducts');
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
            $products_data['shop_name'] = M('shop')->getFieldById($products_data['shop_id'], 'shop_name');
            $shop_products_category_extend = M('shop_products_category_extend');
            $category_extend = $shop_products_category_extend->where(array('products_id' => $id))->find();
            $products_data['shop_category_id'] = $category_extend['category_id'];
            $products_data['shop_category_name'] = M('shop_products_category')->getFieldById($products_data['shop_category_id'], 'name');
            $this->products_data = $products_data;
            $this->display();
        }
    }


    // 非物理删除
    public function delete() {
        if (IS_POST) {
            $products = D('ShopProducts');
            if (!$products->products_delete(I('post.id/d'))) {
                $this->error($products->getError());
            }
            $this->success('删除成功！', U('products_list'));
        }
    }


    // 彻底物理删除
    public function trur_delete() {
        if (IS_POST) {
            $products = D('ShopProducts');
        	if (!$products->trur_products_delete(I('post.id/d'))) {
            	$this->error($products->getError());
        	}
            $this->success('彻底删除成功！', U('products_list'));
        }
    }
}
<?php
/**
 * 云餐品 - 分类 管理控制器
 * Update time：2016-4-19 02:26:08
 */

namespace S\Controller;
use Common\Util\Category;

class CloudProductsCategoryController extends BaseController {

    // 取得所有分类
    public function get_category_call_json() {
    	$category_list = D('CloudProductsCategory')->get_category_call(I('get.shop_type/d'));
        $Category = new Category;
        $category_tree = $Category->create_array_tree($category_list);
        $this->ajaxReturn($category_tree);
    }


    // 修改分类名称
    public function update_name() {
    	if (IS_POST) {
	        $category = D('CloudProductsCategory');
	        if (true === $category->update_name(I('post.id/d'), I('post.name/s')))
	            $this->success('更新成功！');
	        else
	            $this->error($category->getError());
	    }
    }


    // 分类删除
    public function del() {
    	if (IS_POST) {
			$category = D('CloudProductsCategory');
	        if (true === $category->del())
	            $this->success('删除成功！');
	        else
	        	$this->error($category->getError());
    	}
    }


    // 彻底物理删除
    public function trur_delete() {
        if (IS_POST) {
            $category = D('CloudProductsCategory');
        	if (true === $category->trur_delete(I('post.id/d')))
           		$this->success('彻底删除成功！');
           	else
            	$this->error($category->getError());
        }
    }


    // Nestable 插件专用，增删改统一处理  update：2016-4-19 02:25:47
    public function category_edit() {
    	if (IS_POST) {
	        $category = D('CloudProductsCategory');
	        if (true === $category->category_edit())
	        	$this->success('保存成功！');
	        else
	        	$this->error($category->getError());
	    }
    }

}
<?php
/**
 * 云餐品 - 扩展分类 模型
 * Update time：2016-4-28 09:53:16
 */

namespace S\Model;
use Core\Model;

class CloudProductsCategoryExtendModel extends Model {

    protected $insertFields = 'category_id,products_id';
    protected $updateFields = 'category_id,products_id';

    protected $_validate = array(
    	array('category_id', 'check_cloud_category_id', '云分类不合法', self::EXISTS_VALIDATE, 'callback'),
    );

    // 检测云餐品 - 扩展分类是否合法
    protected function check_cloud_category_id($category_id) {
        $cloud_products_category = M('cloud_products_category');
        if ($cloud_products_category->field('id')->find($category_id))
            return true;
        else
            return false;
    }


    // 解除产品，云产品扩展分类的关系（由产品单方面解缔约）
    public function products_extend_del($products_id) {
		if (false === $this->where(array('products_id' => $products_id))->delete()) {
            $this->error = $this->error ? : '解除与云产品扩展分类的关系失败！';
            return false;
        } else {
        	return true;
        }
    }

    // 解除产品，云产品扩展分类的关系（由扩展分类单方面解缔约）
    public function category_extend_del($category_id) {
        if (false === $this->where(array('category_id' => array('in', (array) $category_id)))->delete()) {
            $this->error = $this->error ? : '解除与云产品扩展分类的关系失败！';
            return false;
        } else {
            return true;
        }
    }


    // 添加云产品扩展分类关系
    public function extend_add($data) {
		if (!$this->create($data) || !$this->add()) {
            $this->error = $this->error ? : '添加与云产品扩展分类的关系失败！';
            return false;
        } else {
        	return true;
        }
    }


    // 更新云产品扩展分类关系
    public function extend_up($data) {
    	if (!$this->products_extend_del($data['products_id'])) {
    		$this->error = $this->error ? : '删除与云产品扩展分类的关系失败！';
            return false;
    	}
		if (!$this->extend_add($data)) {
            $this->error = $this->error ? : '更新与云产品扩展分类的关系失败！';
            return false;
        }
        return true;
    }
}
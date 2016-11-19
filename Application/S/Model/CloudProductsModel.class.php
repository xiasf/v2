<?php
/**
 * 云餐品模型
 * Update time：2016-3-1 23:37:18
 */

namespace S\Model;

class CloudProductsModel extends ProductsModel {

    protected $tableName = 'products';

    public function _initialize () {
        $this->_auto = array_merge($this->_auto, array(array('shop_id', 0, self::MODEL_BOTH)));
    }

    public function products_add() {
        if (IS_POST) {
            $this->startTrans();
            if (!$this->create() || !$new_id = $this->add()) {
                $this->rollback();
                $this->error = $this->error ? : '添加失败';
                return false;
            } else {
                $category_id = I('post.cloud_category_id/d');
                $category_extend = D('CloudProductsCategoryExtend');
                if (!$category_extend->extend_add(array('category_id' => $category_id, 'products_id' => $new_id))) {
                    $this->rollback();
                    $this->error = $category_extend->error ? : '添加与云产品扩展分类的关系失败！';
                    return false;
                }
                $this->commit();
                return true;
            }
        }
    }


    public function products_update() {
        if (IS_POST) {
            $id = I('post.id/d');
            if (!$this->field('id')->find($id)) {
                $this->error = '菜品不存在';
                return false;
            }
            $this->startTrans();
            if (!$this->create() || false === $this->save()) {
                $this->rollback();
                $this->error = $this->error ? : '更新失败';
                return false;
            } else {
                $category_id = I('post.cloud_category_id/d');
                $category_extend = D('CloudProductsCategoryExtend');
                if (!$category_extend->extend_up(array('category_id' => $category_id, 'products_id' => $id))) {
                    $this->rollback();
                    $this->error = $category_extend->error ? : '更新与云产品扩展分类的关系失败！';
                    return false;
                }
                $this->commit();
                return true;
            }
        }
    }


    // 非物理删除
    public function products_delete($id) {
        if (IS_POST) {
            if (!$this->field('id')->find($id)) {
                $this->error = '菜品不存在';
                return false;
            }
            $this->startTrans();
            if (!$this->update(array('id' => $id, 'is_del' => 1))) {
                $this->rollback();
                $this->error = $this->error ? : '删除失败';
                return false;
            }

            // 删除与云产品扩展分类的关系
            $category_extend = D('CloudProductsCategoryExtend');
            if (!$category_extend->products_extend_del($id)) {
                $this->rollback();
                $this->error = $category_extend->error ? : '解除与云产品扩展分类的关系失败！';
                return false;
            }

            $this->commit();
            return true;
        }
    }


    // 彻底物理删除
    public function true_products_delete($id) {
        if (IS_POST) {
            if (!$this->field('id')->find($id)) {
                $this->error = '菜品不存在';
                return false;
            }
            $this->startTrans();
            if (!$this->delete($id)) {
                $this->rollback();
                $this->error = $this->error ? : '删除失败';
                return false;
            }

            // 删除与云产品扩展分类的关系
            $category_extend = D('CloudProductsCategoryExtend');
            if (!$category_extend->products_extend_del($id)) {
                $this->rollback();
                $this->error = $category_extend->error ? : '解除与云产品扩展分类的关系失败！';
                return false;
            }

            $this->commit();
            return true;
        }
    }
}
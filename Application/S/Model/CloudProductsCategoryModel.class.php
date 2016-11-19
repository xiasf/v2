<?php
/**
 * 云餐品 - 分类 模型
 * Update time：2016-3-6 17:40:55
 */

namespace S\Model;
use S\Model\CommonModel;
use Common\Util\Category;

class CloudProductsCategoryModel extends CommonModel {

    protected $insertFields = 'name,parent_id,note,sort,shop_type,status';
    protected $updateFields = 'name,parent_id,note,sort,status,is_del';

    protected $_validate = array(
        array('name', '1,15', '菜品分类名称不合法', self::EXISTS_VALIDATE, 'length'),
        array('note', '1,20', '菜品分类备注不合法', self::EXISTS_VALIDATE, 'length'),
        array('shop_type', '1,2,3,4', '商店分类不合法', self::EXISTS_VALIDATE, 'in'),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_UPDATE),
        array('status', 1, self::MODEL_INSERT),
    );


	// 根据经营类别获取全部云分类
	public function get_category_call($shop_type) {
        return $this->where(array('shop_type' => $shop_type))->order('sort asc')->select();
	}

    // 修改分类名称
    public function update_name($id, $name) {
        if ($this->create(array('id' => $id, 'name' => $name)) && false !== $this->save())
            return true;
        else {
            $this->error = $this->error ? : '未知错误';
            return false;
        }
    }


    // 分类删除
    public function del() {
        $goods_list = array();
        $cat_list = $this->_get_all_child_category(I('post.id/d'));
        if (!$cat_list) {
            $this->error = '分类不存在！';
            return false;
        }
        $this->startTrans();
        if (!$this->delete(implode(',', $cat_list))) {
            $this->rollback(); 
            $this->error = $this->error ? : '删除失败！';
            return false;
        }

        $category_extend = D('CloudProductsCategoryExtend');
        if (!$category_extend->category_extend_del($cat_list)) {
			$this->rollback(); 
            $this->error = $category_extend->error ? : '解除与云产品扩展分类的关系失败！';
            return false;
        }

        $this->commit();
        return true;
    }


    // Nestable 插件专用，增删改统一处理  update：2016-3-17 01:06:12
    public function category_edit() {
        $Category = new Category;
        $array = $Category->parse_array_tree(json_decode(trim(I('post.str/s', '', '')), true));
        if (true !== $Category->check_legality($array)) {
            $this->error = '分类数据非法';
            return false;
        }
        $shop_type = I('post.shop_type/d');
        $this->startTrans();
        foreach ($array as $value) {
            if (1 < $Category->array_depth($array, $value['id'])) {
                $this->error = "分类不能大于两级哦 id:{$value['id']} name:{$value['name']}";
                $this->rollback();
                return false;
            }
            $value['shop_type'] = $shop_type;
            if (true !== $this->_category_edit_act($value)) {
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }


    // 编辑分类
    private function _category_edit_act($data) {
        if ($data) {
            if ($data['parent_id'] && !$this->where(array('id' => $data['parent_id']))->lock(true)->find()) {
                $this->error = '上级分类不存在';
                return false;
            }
            if ($data['id'] && !$this->where(array('id' => $data['id']))->lock(true)->find()) {
                $this->error = '分类不存在';
                return false;
            }
            if($data['id']) {
                if (!$this->create($data) || false === $this->save()) {
                    $this->error = $this->error ? : '未知错误';
                    return false;
                }
            } else {
                if (!$this->create($data) || !$this->add()) {
                    $this->error = $this->error ? : '未知错误';
                    return false;
                }
            }
            return true;
        }
    }


    // 取得全部子分类
    private function _get_all_child_category($id) {
        $arr = array($id);
        $items = $this->field('id')->where(array('parent_id' => $id))->select();
        foreach ($items as $value) {
            $arr = array_merge($arr, $this->_get_all_child_category($value['id']));
        }
        return $arr;
    }

}
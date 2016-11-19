<?php
/**
 * 分类处理工具类
 * Update time：2016-3-6 17:40:55
 */

namespace Common\Util;

class Category {

	public $tableName 	= 'category';	// 分类表名
	public $tablePrefix = 'tb_';		// 分类表前缀

	public $current_id 	= 'id';			// 当前标示 字段名
	public $parent_id 	= 'parent_id';		// 父级标示 字段名
	public $level 		= 1;			// 初始级别
	public $top 		= 0;			// 顶级标示
	public $depth 		= 1;			// 获取深度，0为不限制
	public $where 		= array();		// 条件
	public $order 		= array();		// 排序
	public $page 		= array();		// 分页
	public $limit 		= null;			// 指定查询


    public function __construct($arr = array()) {
    	isset($arr['current_id']) 	&& $this->$current_id;
    	isset($arr['parent_id']) 	&& $this->$parent_id;
    	isset($arr['level']) 		&& $this->$level;
    	isset($arr['top']) 			&& $this->$top;
    	isset($arr['where']) 		&& $this->$where;
    	isset($arr['order']) 		&& $this->$order;
    	isset($arr['page']) 		&& $this->$page;
    	isset($arr['limit']) 		&& $this->$limit;
    }


	private function getModel() {
		static $_model = null;
		if (!isset($_model)) {
			$_model = M($tableName, $tablePrefix);
		}
		return $_model;
	}


    /**
     * 获取顶级分类
     * @access public
	 * @return array
	 */
	public function getCategoryListTop() {
		return self::getChildCategoryList(self::$top);
	}


    /**
     * 获取子分类
     * @access public
	 * @return array
	 */
	public function getChildCategoryList($parent_id = 0, $limit = 0, $page = array(), $depth = 1) {
		$model = $this->getModel();
		$model->where();
		$model->limit();
		$model->page();
		$model->order();
	}


	// 获取全部同级分类
	private function get_on_category_list($id) {
		$model = $this->getModel();
		$on_category = $model->find($id);
		$on_category_list = $model->where(array($this->parent_id => $on_category[$this->parent_id], array($this->$current_id, array('neq' => $id))))->select();
		return $on_category_list;
	}


	// 获取全部父级分类
	private function get_parent_category_list($pid) {
		$article_category = $this->getModel();
		$parent = array();
		if ($tem = $article_category->find($pid)) {
			$parent[] = $tem;
			$parent = array_merge($this->get_parent_category_list($tem['parent_id']), $parent);
		}
		return $parent;
	}


	// 获取子级分类（不是全部）
	public function get_chid_category($id) {
		$model = $this->getModel();
		$chid_category_list = $model->where(array($this->parent_id => $id, array($this->$current_id, array('neq' => $id))))->select();
		return $chid_category_list;
	}


	/*
	// 取得全部子分类
	private function category_x($id) {
		$arr = array();
		$arr[] = $id;
		$query = new IQuery("seller_category");
		$query->where = "parent_id = ".$id.' and seller_id = ' . $this->seller['seller_id'];
		$items = $query->find();
		foreach ($items as $value) {
			$arr = array_merge($arr, $this->category_x($value['id']));
		}
		return $arr;
	}
	*/

	// 获取全部子分类id（包含自身ID）
	public function get_all_chid_category($id) {
		$result = array($id);
		$catDB  = $this->getModel();
		while(true)
		{
			$id = current($result);
			if(!$id)
			{
				break;
			}
			$temp = $catDB->where(array($this->parent_id => $id))->select();
			foreach($temp as $key => $val)
			{
				$result[] = $val['id'];
			}
			next($result);
		}
		return $result;
	}


	/**
	* 对数组按键进行排序 （不在总量计算中的排序没有什么实际意义——不能代表总体中的排序）
	* @access public
	* @param array $arr 查询结果
	* @param string $field 排序的字段名
	* @param array $sortby 排序类型
	* @return array
	*/
	public function list_sort_by($arr, $field, $sortby='asc') {
	   if (is_array($arr)) {
	       $refer = $resultSet = array();
	       foreach ($arr as $i => $data)
	           $refer[$i] = &$data[$field];
	       switch ($sortby) {
	           case 'asc': // 正向排序
	                asort($refer);
	                break;
	           case 'desc':// 逆向排序
	                arsort($refer);
	                break;
	           case 'nat': // 自然排序
	                natcasesort($refer);
	                break;
	       }
	       foreach ($refer as $key => $val)
	           $resultSet[] = &$arr[$key];
	       return $resultSet;
	   }
	   return false;
	}


    /**
     * 无限分类处理 形成“单树” “有树干和级数”
	 * @static
     * @access public
     * @param array 	$cate 	要处理的分类数组
	 * @param int 		$pid  	分类PID
	 * @param string 	$html 	分类标示
	 * @param int 		$level 	分类层级
	 * @return array
	 */
	public function unlimitedForLevel($cate, $pid = 0, $html = '├─', $level = 0) {
		$arr = array();
		foreach ($cate as $k => $value) {
			if ($value['parent_id'] == $pid) {
                unset($cate[$k]);
				$value['level'] = $level + 1;
				$value['html'] = str_repeat('　', $level) . $html;
				$arr[] = $value;
				$arr = array_merge($arr, $this->unlimitedForLevel($cate, $value['id'], $html, $value['level']));
			}
		}
		return $arr;
	}

	// unlimitedForLevel维持原有顺序的实现
	public function  unlimitedForLevel_($arr) {
		foreach ($arr as $key => &$value) {
			$depth = $this->array_depth($arr, $value['id']);
			$value['level'] = $depth;
			$value['html'] = str_repeat('　', $depth) . '├─';
		}
		return $arr;
	}


    /**
     * 计算某一（层）数组的级数（深度）
     * @access public
     * @param array 	$arr 	目标数组
  	 * @param int 		$id 	待计算的数组层ID
	 * @return int 		$depth  目标深度	
	 */
	public function array_depth($arr, $id) {
		static $tem = array();
		if (!$tem) {
			foreach ($arr as $key => $value) {
				$tem[$value['id']] = $value;
			}
		}
		$pid = $tem[$id][$this->parent_id];
		$depth = 1;
		if($pid != 0 && $tem[$pid]) {
			$depth = $this->array_depth($tem, $pid) + 1;
		}
		return $depth;
	}


	// 同上 array_depth —— sql版本
	public function array_depth_sql($arr, $id) {
		$model = $this->getModel();
		$depth = 1;
		$pid = $model->getFieldById($id, 'pid');
		$id = $model->getFieldById($pid, 'id');
		if($pid != 0 && $id) {
			$depth = $this->array_depth_sql($pid) + 1;
		}
		return $depth;
	}


	/**
     * 计算层级数组最大深度
     * @access public
     * @param array 	$arr 	目标数组
	 * @return int 		$max_depth  最大深度	
	 */
	public function array_max_depth($arr) {
		$max_depth = 1;
		$depth = 0;
		foreach ($array as $value) {
			if (isset($value['pid'])) {
				$depth = $this->array_max_depth($value) + 1;
				if ($depth > $max_depth) {
					$max_depth = $depth;
				}
			}
		}
		return $max_depth;
	}


	/**
     * 任意层级数组 转 树结构数组
     * 数组结构必须包含pid层级关系和顺序 此顺序非sort排序，数组本身就是排序，请自行决定从数据库中读取出来的顺序即可）
     * @access public
     * @param array 	$arr 	目标数组
  	 * @param int 		$pid 	树结构起点
	 * @return array    $return    树结构数组	
	 */
	public function create_array_tree($arr, $pid = 0) {
        $return = array();
        foreach($arr as $k => $value) {
            if($value[$this->parent_id] == $pid) {
                unset($arr[$k]);
                $value['children'] = $this->create_array_tree($arr, $value['id']);
                if (!$value['children']) unset($value['children']);
                $return[] = $value;
            }
        }
        return $return;
    }


	/**
     * 树结构数组 转 层级数组
     * @access public
     * @param array 	$arr 	目标数组
  	 * @param int 		$pid 	数组（层）起点
	 * @return array    $return    层级数组	
	 */
	public function parse_array_tree($arr, $pid = 0) {
		$sort = 1; 	// 同级排序
		$return = array();
		foreach ($arr as $key => $value) {
			$value['sort'] = $sort++;
			$value[$this->parent_id] = $pid;
			$tem = isset($value['children']) ? $value['children'] : null;
			if(isset($value['children'])) unset($value['children']);
			$return[] = $value;
			if (isset($tem) && is_array($tem))
				$return = array_merge($return, $this->parse_array_tree($tem, $value['id']));
		}
		return $return;
	}


    /**
     * 合法性检查
     * @access public
     * @param array 	$arr 	待检查的数组
	 * @return array or true 	返回true时合法（返回array为非法数据）
	 */
	public function check_legality($arr) {
		$tem = array();
		foreach ($arr as $key => $value) {
			// 当 id parent_id非法
			if(($value['id'] == $value[$this->parent_id] && 0 != $value['id']) || $value[$this->parent_id] < 0 || $value['id'] < 0)
				return $value;
			// 记录可能成为父级的ID
			if (0 != $value['id']) $tem[] = $value['id'];
		}

		foreach ($arr as $key => $value) {
			// 当 parent_id没有定义时非法
			if ($value[$this->parent_id] != 0 && !in_array($value[$this->parent_id], $tem))
				return $value;
		}
		return true;
	}


    /**
     * 合法性检查 类似check_legality 不过数据源为数据库
     * @access public
	 * @return array or true 	返回true时合法
	 */
	public function check_category() {
		$model = $this->getModel();
		return $model->where(array(array('pid' => array('eq', array('exp'=>'id')), array('pid' => array('neq', array('exp'=>'id')), 'pid' => array('neq' => 0)))))->select();
	}
}
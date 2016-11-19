<?php
/**
 * 百度LBS云接口
 * updata：2016-4-20 21:09:19
 */

namespace Common\Util;

class BaiduMapLbs {

	public $ak = 'E9o6XOIa9tDfc9ix8gW4h5y4';
	public $geotable_id = 134032;	// 表ID

	public function __construct($ak = '', $geotable_id = '') {
		if (!empty($ak)) {
			$this->ak = $sk;
		}
		if (!empty($geotable_id)) {
			$this->geotable_id = $geotable_id;
		}
	}

	// LBS poi周边搜索
	public function so_lbs($where = array()) {
		$url = 'http://api.map.baidu.com/geosearch/v3/nearby?';
		$params = array(
			'q' => '',
			'tag'=>'',
			'filter'=>'',
			'location' => '',
			'radius' => 1000,
			'coord_type' => 3,
			'page_size' => 50,
			'page_index' => 0,
			'sortby'=>'distance:1',
			'output'=>'json',
		);
		$where = array_merge($params, $where);
		$url .= $this->create_query($where);
		$result = file_get_contents($url);
		return $result;
	}


	/** ######  表结构操作  ##### **/

	// 创建表（create geotable）接口
	public function create_geotable($data) {
		$url = 'http://api.map.baidu.com/geodata/v3/geotable/create';
		$data = $this->create_query($data);
		return $this->post($url, $data);
	}

	// 查询表（list geotable）接口
	public function get_geotable($params = array()) {
		$url = 'http://api.map.baidu.com/geodata/v3/geotable/list?';
		$url .= $this->create_query($params);
		$result = file_get_contents($url);
		return $result;
	}

	// 查询指定id表（detail geotable）接口
	public function detail_geotable($params = array()) {
		$url = 'http://api.map.baidu.com/geodata/v3/geotable/detail?';
		$url .= $this->create_query($params);
		$result = file_get_contents($url);
		return $result;
	}

	// 修改表（update geotable）接口
	public function up_geotable($data) {
		$url = 'http://api.map.baidu.com/geodata/v3/geotable/update';
		$data = $this->create_query($data);
		return $this->post($url, $data);
	}

	// 删除表（geotable）接口
	public function del_geotable($data) {
		$url = 'http://api.map.baidu.com/geodata/v3/geotable/delete';
		$data = $this->create_query($data);
		return $this->post($url, $data);
	}

	// 创建列（create column）接口
	public function create_column($data) {
		$url = 'http://api.map.baidu.com/geodata/v3/column/create';
		$data = $this->create_query($data);
		return $this->post($url, $data);
	}

	// 删除指定条件列（column）接口
	public function del_column($where) {
		$url = 'http://api.map.baidu.com/geodata/v3/column/delete';
		$data = $this->create_query($where);
		return $this->post($url, $data);
	}

	// 修改指定条件列（column）接口
	public function up_column($data) {
		$url = 'http://api.map.baidu.com/geodata/v3/column/update';
		$data = $this->create_query($data);
		return $this->post($url, $data);
	}

	// 查询列（list column）接口
	public function detail_column_list($params = array()) {
		$url = 'http://api.map.baidu.com/geodata/v3/column/list?';
		$url .= $this->create_query($params);
		$result = file_get_contents($url);
		return $result;
	}

	// 查询指定id列（detail column）详情接口
	public function detail_column($id) {
		$url = 'http://api.map.baidu.com/geodata/v3/column/detail';
		$params = array(
			'id' => $id,
		);
		$url .= $this->create_query($params);
		$result = file_get_contents($url);
		return $result;
	}
	/** ######  表结构操作  ##### **/



	/** ######  数据操作  ##### **/

	// 创建数据（create poi）接口
	public function create_poi($data) {
		$url = 'http://api.map.baidu.com/geodata/v3/poi/create';
		$params = array(
			'coord_type' => 3,
		);
		$data = array_merge($params, $data);
		$data = $this->create_query($data);
		return $this->post($url, $data);
	}

	// 删除数据（poi）接口（支持批量）
	public function del_poi($where) {
		$url = 'http://api.map.baidu.com/geodata/v3/poi/delete';
		$data = $this->create_query($where);
		return $this->post($url, $data);
	}

	// 修改数据（poi）接口
	public function up_poi($data) {
		$url = 'http://api.map.baidu.com/geodata/v3/poi/update';
		$params = array(
			'coord_type' => 3,
		);
		$data = array_merge($params, $data);
		$data = $this->create_query($data);
		return $this->post($url, $data);
	}

	// LBS poi详情检索
	public function detail_poi($poi_id) {
		$url = 'http://api.map.baidu.com/geosearch/v3/detail/'.$poi_id.'?';
		$url .= $this->create_query();
		$result = file_get_contents($url);
		return $result;
	}

	// 查询指定条件的数据（poi）列表接口
	public function so_poi($where) {
		$url = 'http://api.map.baidu.com/geodata/v3/poi/list?';
		$url .= $this->create_query($where);
		$result = file_get_contents($url);
		return $result;
	}

	/** ######  数据操作  ##### **/


	// 生成请求url
	private function create_query($data = array()) {
		$params = array(
			'ak' => $this->ak,
			'geotable_id'=> $this->geotable_id,
		);
		if (!empty($data))
			$params = array_merge($params, $data);
		return http_build_query($params);
	}

	// 模拟提交数据方法
	private function post($url, $data) {
	    $curl = curl_init(); // 启动一个CURL会话      
	    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址                  
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检测    
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在      
	    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览
	    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:')); //解决数据包大不能提交     
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转      
	    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer      
	    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求      
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包      
	    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循     
	    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容      
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回 
	    $tmpInfo = curl_exec($curl); // 执行操作      
	    if (curl_errno($curl)) {
	    	return '{"status":-1,"message":"'.curl_error($curl).'"}';
	    }
	    curl_close($curl); // 关闭CURL会话      
	    return $tmpInfo; // 返回数据      
	}
}
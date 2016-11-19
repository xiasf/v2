<?php
/**
 * 订单打印服务
 * Update time：2016-4-28 15:05:31
 */
namespace S\Server;

class OrderPrintServer {

	/**
	 * 【cmd】判断收到的推送消息中cmd的标示，用于业务判断
	 * finish	打印完成状态推送
	 * userrequest	按键上报请求 request1-按键功能1，request2-按键功能2
	 * print_state	打印机实时状态 （暂未发布）
	 */

	protected $post_url 				= 'http://open.10ss.net:8888';					// 接口地址
	protected $get_printer_status_url 	= 'http://open.10ss.net:8888/getstatus.php';	// 获取打印机状态
	protected $add_printer_url 			= 'http://open.10ss.net:8888/addprint.php';		// 添加打印机接口
	protected $del_printer_url 			= 'http://open.10ss.net:8888/removeprint.php';	// 删除打印机接口

	protected $apiKey 					= '413c703ceb8559143857a183283c86929c207591';	// apiKey
	protected $partner 					= 4099;											// 用户id
	protected $username 				= 'xiak';										// 用户名


	// 删除打印机
	public function del_printer($machine_code, $msign) {
		$params = array(
			'machine_code' => $machine_code,
			'partner' => $this->partner,
		);
		$params['sign'] = $this->generateSign($params, $this->apiKey, $msign);
		$data = http_build_query($params);
		$status =  $this->post($this->del_printer_url, $data);
		$message = array(
			1 => '删除成功',
			2 => '没这个设备',
			3 => '删除失败',
			4 => '认证失败',

			-1 => 'curl错误',
		);
		return array('status' => $status, 'info'=> $message[$status]);
	}


	// 添加打印机
	public function add_printer($data) {
		$params = array(
			'partner' => $this->partner,
			'username' => $this->username,
		);
		$params = array_merge($params, $data);
		$params['sign'] = strtoupper(md5($this->apiKey . 'partner' . $this->partner . 'machine_code' . $params['machine_code'] . 'username' . $params['username'] . 'printname' . $params['printname'] . 'mobilephone' . $params['mobilephone'] . 'msign' . $params['msign'] . $params['msign']));
		$data = http_build_query($params);
		$status =  $this->post($this->add_printer_url, $data);
		$message = array(
			1 => '添加成功',
			2 => '重复',
			3 => '添加失败',
			4 => '添加失败',
			5 => '用户验证失败',
			6 => '非法终端号',

			-1 => 'curl错误',
		);
		return array('status' => $status, 'info'=> $message[$status]);
	}


	// 获取打印机状态
	public function get_printer_status($machine_code, $mKey) {
		$params = array(
			'machine_code' => $machine_code,
			'partner' => $this->partner,
		);
		$params['sign'] = $this->generateSign($params, $this->apiKey, $mKey);
		$data = http_build_query($params);
		$status = $this->post($this->get_printer_status_url, $data);
		if (!is_numeric($status)) {
			$status = substr($status, 7);
		}
		$message = array(
			0 => '离线',
			1 => '在线',
			2 => '缺纸',

			-1 => 'curl错误',
		);
		return array('status' => $status, 'info'=> $message[$status]);
	}

	// 打印推送
	public function print_send($content, $machine_code, $mKey) {
	    $params = array(
            'machine_code' => $machine_code,
            'partner' => $this->partner,
            'time' => NOW_TIME
	    );
	    $params['sign'] = $this->generateSign($params, $this->apiKey, $mKey);
	    $params['content'] = $content;

	    $data = http_build_query($params);
		$result = $this->post($this->post_url, $data);
		if (!is_numeric($result)) {
			$result = json_decode($result, true);
			$status = $result['state'];
		} else {
			$status = $result;
		}
		$message = array(
			1 => '数据提交成功',
			2 => '提交时间超时',
			3 => '参数有误。',
			4 => 'sign加密验证失败。',

			-1 => 'curl错误',
		);
		$arr = array('status' => $status, 'info'=> $message[$status]);
		if ($status == 1)
			$arr['id'] = $result['id'];
		return $arr;
	}

	// 发送请求
	private function post($url, $data) { // 模拟提交数据函数      
	    $curl = curl_init(); // 启动一个CURL会话      
	    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址                  
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检测    
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在      
	    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览
	    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Expect:')); //解决数据包大不能提交     
	    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转      
	    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer      
	    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求      
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包      
	    curl_setopt($curl, CURLOPT_COOKIEFILE, $GLOBALS['cookie_file']); // 读取上面所储存的Cookie信息      
	    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循     
	    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容      
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回 

	    $tmpInfo = curl_exec($curl); // 执行操作      
	    if (curl_errno($curl)) {
	    	// return '{"status":-1,"info":"'.curl_error($curl).'"}';
	    	return -1;
	    }
	    curl_close($curl); // 关闭CURL会话      
	    return $tmpInfo; // 返回数据      
	}

	// 生成sign
	private function generateSign($params, $apiKey, $msign) {
	    //所有请求参数按照字母先后顺序排
	    ksort($params);
	    //定义字符串开始所包括的字符串
	    $stringToBeSigned = $apiKey;
	    //把所有参数名和参数值串在一起
	    foreach ($params as $k => $v)
	    {
	        $stringToBeSigned .= urldecode($k.$v);
	    }
	    unset($k, $v);
	    //定义字符串结尾所包括的字符串
	    $stringToBeSigned .= $msign;
	    //使用MD5进行加密，再转化成大写
	    return strtoupper(md5($stringToBeSigned));
	}

	// 上报sign验证
	private function cmd_check_sign($time, $sign) {
		return strtoupper(md5($this->apiKey . $time)) == $sign;
	}
}
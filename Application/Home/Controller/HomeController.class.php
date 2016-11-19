<?php
namespace Home\Controller;
use Core\Controller;
use Common\Util\BaiduMapLbs;
/**
 * home控制器
 * Update time：2016-4-8 16:57:02
 */
class HomeController extends Controller {

	protected function _initialize() {
	}

	// LBS云检索
	public function so_lbs() {
		$location = I('get.location');
		$BaiduMapLbs = new BaiduMapLbs;
		$where = array(
			// 'q' => '',
			'location'=> $location,
			'radius' => 5000,
			// 'tags' => '',
			// 'filter' => 'address_reference|shop_business_type|shop_type|shop_id'
		);
		// 要排序在这里就要得出排序好的，否则下面排序，不在总量计算中排序没有什么实际意义
		$result = $BaiduMapLbs->so_lbs($where);
		$arr = json_decode($result, true);
		$tem = array(
			'status' => $arr['status'],
			'total' => $arr['total'],
			'size' => $arr['size'],
			'contents' => array(),
		);
		if ($arr['status'] == 0 && !empty($arr['contents'])) {
			$shop_model = M('shop');
			foreach ($arr['contents'] as &$value) {
				$shop_id = $value['shop_id'];
				$shop_info = $shop_model->field('logo,store_bitmap,shop_business_type,shop_type,business_hours')->find($shop_id);
				if ($shop_info) {
					if ($shop_info['store_bitmap'])
						$shop_info['store_bitmap'] = get_thumb_src($shop_info['store_bitmap'], 420, 260, 'jpg', 6, 100);
					$shop_info['shop_type_name'] = get_shop_type($shop_info['shop_type']);
					$value = array_merge($value, $shop_info);
				} else {
					unset($value);
				}
			}
			$tem['contents'] = $arr['contents'];
			$tem['total'] = count($tem['contents']);
		}
		echo json_encode($tem);

		// 数据处理（营业时间计算等）
		// ……
		// echo $result;
	}
}
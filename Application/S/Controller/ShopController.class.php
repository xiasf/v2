<?php
/**
 * 分店管理控制器
 * Update time：2016-3-6 17:40:55
 */

namespace S\Controller;
use Core\Page;

class ShopController extends BaseController {

    // 获取商店打印机列表
    public function get_printer_list() {
        $shop_id = I('get.shop_id/d');
        $shop = M('shop s');
        $result = $shop
        ->join('__SHOP_PRINTER__ sp ON sp.shop_id = s.id')
        ->join('__PRINTER__ p ON sp.printer_id = p.id')
        ->field('p.*')
        ->where(array('s.id' => $shop_id))
        ->order('p.id desc')
        ->select();
        $this->ajaxReturn($result);
    }


    // 获取商店的打印模式
    public function get_print_mode() {
        $result = array('status' => 0, 'print_mode' => '', 'info' => '');
        $print_mode = M('shop')->getFieldById(I('get.shop_id/d'), 'print_mode');
        if (isset($print_mode)) {
            $result['print_mode'] = $print_mode;
            $result['status'] = 1;
        } else {
            $result['info'] = '获取商店的打印模式错误！';
        }
        $this->ajaxReturn($result);
    }


    // 设置商店的打印模式
    public function set_print_mode() {
        if (IS_POST) {
            $shop_id = I('post.shop_id/d');
            $print_mode = I('post.print_mode/d');
            $shop = D('shop');
            if (!$shop->set_print_mode($shop_id, $print_mode)) {
                $this->error($shop->getError());
            } else {
                $this->success('设置成功！');
            }
        }
    }


    // 绑定打印机
    public function bind_printer() {
        if (IS_POST) {
            $shop_id = I('post.shop_id/d');
            $printer_id = I('post.printer_id/d');
            $shop_printer = D('ShopPrinter');
            if (!$shop_printer->extend_add(array('shop_id' => $shop_id, 'printer_id' => $printer_id))) {
                $this->error($shop_printer->getError());
            } else {
                $this->success('绑定成功！');
            }
        }
    }


    // 解绑打印机
    public function unbundling_printer() {
        if (IS_POST) {
            $shop_id = I('post.shop_id/d');
            $printer_id = I('post.printer_id/d');
            $shop_printer = D('ShopPrinter');
            if (!$shop_printer->shop_printer_extend_del($shop_id, $printer_id)) {
                $this->error($shop_printer->getError());
            } else {
                $this->success('解绑成功！');
            }
        }
    }


    // 检测远程数据
    public function baidu_load() {
        $result = array('status' => 0, 'info' => '');
        $shop = D('shop');
        $poi = $shop->so_poi(array('shop_id' => I('get.shop_id/d')));
        $poi = json_decode($poi, true);
        if ($poi['status'] == 0 && $poi['total']) {
            $result['status'] = 1;
        }
        $this->ajaxReturn($result);
    }

	public function shop_list() {
        $shop = M('shop s');
        $this->shop_list = $shop
        ->join('LEFT JOIN __SELLER__ se ON s.seller_id = se.id')
        ->field('s.*,se.id as seller_id,se.seller_name,se.logo as seller_logo')
        ->page(I('get.page/d'), 10)
        ->order('s.sort asc, s.id desc')
        ->select();

        $this->shop_count = $shop
        ->join('LEFT JOIN __SELLER__ se ON s.seller_id = se.id')
        ->count('s.id');
        $page = new Page($this->shop_count, 10);
        $this->page_show = $page->show();
		$this->display();
	}


	public function add() {
        if (IS_POST) {
            // note: 减少了一行代码，并且还能提高程序兼容性
            // $return = array('status' => 1, 'info' => '添加成功！');
        	$shop = D('shop');
        	if (!$shop->shop_add()) {
				// $return = array('status' => 0, 'info' => $shop->getError());
                $this->error($shop->getError());
        	}
            $this->success('添加成功！', U('shop_list'));
            // $this->ajaxReturn($return);
        } else {
            $this->display();
        }
    }


    public function update() {
        $shop = D('shop');
        if (IS_POST) {
            if (!$shop->shop_update()) {
                $this->error($shop->getError());
        	}
            $this->success('更新成功！', U('shop_list'));
        } else {
            $id = I('get.id/d');
            if (!$shop_data = $shop->field(true)->find($id)) {
                $this->error('分店不存在！');
            } else {
                $shop_data['seller_name'] = M('seller')->getFieldById($shop_data['seller_id'], 'seller_name') ? : '自营店铺由平台管理';
            }
            $this->shop_data = $shop_data;
            $this->display();
        }
    }


    // 非物理删除
    public function delete() {
        if (IS_POST) {
            $shop = D('shop');
            if (!$shop->shop_delete(I('post.id/d'))) {
                $this->error($shop->getError());
            }
            $this->success('删除成功！', U('shop_list'));
        }
    }


    // 彻底物理删除
    public function trur_delete() {
        if (IS_POST) {
            $shop = D('shop');
        	if (!$shop->true_shop_delete(I('post.id/d'))) {
            	$this->error($shop->getError());
        	}
            $this->success('彻底删除成功！', U('shop_list'));
        }
    }
}
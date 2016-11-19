<?php
/**
 * 打印机管理控制器
 * Update time：2016-3-6 17:40:55
 */

namespace S\Controller;
use Core\Page;

class PrinterController extends BaseController {

	public function printer_list() {
        $printer = M('printer');
        $this->printer_list = $printer->page(I('get.page/d'), 10)->order('id desc')->select();
        $this->printer_count = $printer->count('id');
        $page = new Page($this->printer_count, 10);
        $this->page_show = $page->show();
		$this->display();
	}


    // 测试打印机
    public function send_check() {
        $printer = D('Printer');
        $result = $printer->send_check(I('post.id/d'), I('post.content/s'));
        $this->ajaxReturn($result);
    }


    // 检测远程数据
    public function printer_check() {
        $printer = D('Printer');
        $result = $printer->printer_check(I('get.id/d'));
        $this->ajaxReturn($result);
    }


	public function add() {
        if (IS_POST) {
        	$printer = D('Printer');
        	if (!$printer->printer_add()) {
                $this->error($printer->getError());
        	}
            $this->success('添加成功！', U('printer_list'));
        } else {
            $this->display();
        }
    }


    public function update() {
        $printer = D('Printer');
        if (IS_POST) {
            if (!$printer->printer_update()) {
                $this->error($printer->getError());
        	}
            $this->success('更新成功！', U('printer_list'));
        } else {
            $id = I('get.id/d');
            if (!$this->printer_data = $printer->field(true)->find($id))
                $this->error('分店不存在！');
            $this->display();
        }
    }


    // 非物理删除
    public function delete() {
        if (IS_POST) {
            $printer = D('Printer');
            if (!$printer->printer_delete(I('post.id/d'))) {
                $this->error($printer->getError());
            }
            $this->success('删除成功！', U('printer_list'));
        }
    }


    // 彻底物理删除
    public function trur_delete() {
        if (IS_POST) {
            $printer = D('Printer');
        	if (!$printer->true_printer_delete(I('post.id/d'))) {
            	$this->error($printer->getError());
        	}
            $this->success('彻底删除成功！', U('printer_list'));
        }
    }
}
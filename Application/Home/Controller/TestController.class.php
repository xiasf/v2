<?php
namespace Home\Controller;
use Core\Controller;

/**
 * test控制器
 * Update time：2016-6-18 17:55:33
 */
class TestController extends Controller {
    protected function _initialize() {
        // C('SHOW_PAGE_TRACE', true);
    }

    public function index() {
        $shop = M('shop s');
        $result = $shop
        ->join('INNER JOIN __SHOP_PRINTER__ sp ON sp.shop_id = s.id')
        ->join('INNER JOIN __PRINTER__ p ON sp.printer_id = p.id')
        ->join('LEFT JOIN __ORDER_PAPER__ op ON op.printer_id = p.id and op.print_status = 0')
        ->field('p.*,count(op.printer_id) as vv')
        ->where(array('s.id' => 3))
        ->limit(1)
        // ->order('vv asc')
        ->select();
        print_r($result);
    }


    
}
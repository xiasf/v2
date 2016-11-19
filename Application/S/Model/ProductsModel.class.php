<?php
/**
 * 餐品基础模型
 * Update time：2016-3-1 23:37:18
 */

namespace S\Model;

class ProductsModel extends CommonModel {

    protected $insertFields = 'shop_id,name,category_id,price,unit,img,inventory,describe,status,sort';
    protected $updateFields = 'shop_id,name,category_id,price,unit,img,inventory,describe,status,sort,is_del';

    protected $_validate = array(
        array('name', '1,15', '菜品名称不合法', self::EXISTS_VALIDATE, 'length'),
        array('describe', '1,100', '描述不合法', self::EXISTS_VALIDATE, 'length'),
        array('img', 'check_img', '图片不合法', self::EXISTS_VALIDATE, 'callback'),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('update_time', NOW_TIME, self::MODEL_UPDATE),
        array('status', 'get_status', self::MODEL_BOTH, 'callback'),
    );
}
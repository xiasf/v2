<?php
/**
 * 图片模型
 * 负责图片的上传
 */

namespace Common\Model;
use Core\Model;
use Common\Util\hsms\hsms;

class SmsVerificationCodeModel extends Model{

    protected $insertFields = 'mobile,business_code';
    protected $updateFields = 'id,status';


    protected $_validate = array(
		array('mobile', 'mobile', '手机号码格式错误', 1, 'regex'),
    );

    protected $_auto = array(
		array('create_time', NOW_TIME, self::MODEL_INSERT),
		array('status', 0, self::MODEL_INSERT),
		array('code', 'create_code', self::MODEL_INSERT, 'callback'),
		array('check_time', NOW_TIME, self::MODEL_UPDATE),
    );


    protected function create_code() {
    	return rand(9999, 1000);
    }


    public function send_sms($mobile, $business_code) {
    	$data = [
    		'mobile' => $mobile,
    		'business_code' => $business_code,
    	];

        $s = $this->where(array('mobile' => $mobile, 'business_code' => $business_code))->order('id desc')->find();
        if ($s && ($s['create_time'] + 60) > NOW_TIME) {
            $this->error = '发送间隔不能小于60秒';
            return false;
        }

    	$this->startTrans();
    	// $this->where(array('mobile' => $mobile, 'business_code' => $business_code))->delete();

    	if (($data = $this->create($data)) && $this->add()) {
    		// 发送短信
	        $hsms_tpl_data = array(
	            '{code}' => $data['code'],
	        );
	        $result = hsms::send($data['mobile'], 'sms_verification_code', $hsms_tpl_data);
	        if ('success' == $result) {
	        	$this->commit();
	        	return true;
	        } else {
	        	$this->rollback();
	        	$this->error = '[' . $result['SubmitResult']['code'] . ']' . $result['SubmitResult']['msg'];
	        	return false;
	        }
    	} else {
    		$this->rollback();
    		$this->error = $this->error ? : '发送失败';
    		return false;
    	}
    }

    // 验证code
    public function check_sms($mobile, $code, $business_code) {
    	$map['mobile'] = $mobile;
        $map['code'] = $code;
    	$map['business_code'] = $business_code;
        // $map['status'] = 0;
		$map['create_time'] = ['gt', NOW_TIME - (5 * 60)];
		$data = ['status' => 1];
    	if ($this->where($map)->order('id desc')->find() && false !== $this->where($map)->order('id desc')->save($data)) {
    		return true;
    	} else {
    		return false;
    	}
    }

}
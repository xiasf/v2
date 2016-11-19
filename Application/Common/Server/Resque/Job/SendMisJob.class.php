<?php
namespace Common\Resque\Job;
/**
 * 消息发送队列
 */
class SendMisJob {
	public function setUp()
    {
        // ... Set up environment for this job
    }
 
    public function perform()
    {
    	$params = $this->args;
        // sleep(5);
        // // fwrite(STDOUT, 'Hello!');
        // $p=$this->args['name'];
        // echo'ttime '.$p;
        // // print_r(json_decode($p));
        // echo"string";
    }
 
    public function tearDown()
    {
        // ... Remove environment for this job
    }
}
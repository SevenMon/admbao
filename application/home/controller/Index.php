<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/16
 * Time: 10:06
 */
namespace app\home\controller;

use think\Controller;
use think\Db;

use app\home\controller\Base;

class Index extends Base {
	public function index(){
	    $img = Db::name('signup_banner')->where(array('status' => 1))->select();
	    $data = Db::name('signup_compete')->where(array('status' => 1))->select();
	    foreach ($data as &$value){
            $value['age'] = json_decode($value['age'],true);
        }
	    $this->assign('data',$data);
	    $this->assign('img',$img);
		return $this->fetch('index');
	}

	public function notice(){
	    $compete_id = $this->request->get('id', '');
        $data_notice = Db::name('signup_compete_notice')->where(array('compete_id' => $compete_id))->find();
        $data = Db::name('signup_compete')->find($compete_id);
        $data['age'] = json_decode($data['age'],true);
        $this->assign('id',$compete_id);
        $this->assign('data',$data);
        $this->assign('data_notice',$data_notice);
        return $this->fetch('notice');
    }
    public function know(){
        $compete_id = $this->request->get('id', '');
        $know_content = Db::name('signup_system')->where(array('id' => 1))->find();
        $this->assign('id',$compete_id);
        $this->assign('content',$know_content['know']);
        return $this->fetch('know');
    }
    public function user(){
		$user_id = $this->userId;
		$user_info = getUserAllInfo($user_id);

		$where = array(
		    'user_id' => $user_id,
            'status' => 1
        );
		$order_list = Db::name('signup_order')->where($where)->select();

		$prize_list = Db::name('signup_prize_user')->where(array('user_id' => $user_id))->select();

		$this->assign('order_list',$order_list);
		$this->assign('prize_list',$prize_list);
		$this->assign('user_info',$user_info);
		return $this->fetch('user');
	}
}
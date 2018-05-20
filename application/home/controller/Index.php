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
	    $data = Db::name('signup_compete')->where(array('status' => 1))->select();
	    foreach ($data as &$value){
            $value['age'] = json_decode($value['age'],true);
        }
	    $this->assign('data',$data);
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
        $this->assign('id',$compete_id);
        return $this->fetch('know');
    }
}
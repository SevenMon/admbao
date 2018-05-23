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

class Prize extends Base {
	public function index(){
	    $user_id = 1;
	    $prize = Db::name('signup_prize')->order('id','asc')->select();
	    $prize_people = Db::name('signup_prize_user')->limit(8)->select();
	    $prize_active = Db::name('signup_prize_active')->where(array('status' => 1))->find();

        $num = Db::name('signup_prize_user')->where(array('prize_active_id' => $prize_active['id'],'user_id' => $user_id))->count();
	    $rate = array();
	    $prize_name = array();
	    $prize_id = array();
	    foreach ($prize as $value){
            $rate[] = $value['rate'].'%';
            $prize_name[] = $value['name'];
            $prize_id[] = $value['id'];
        }

        $can = 0;
        if(strtotime($prize_active['start_time']) < time() && strtotime($prize_active['end_time']) > time()){
            $can = 1;
        }

	    $this->assign('can',$can);
	    $this->assign('num',$num);
	    $this->assign('prize_id',json_encode($prize_id));
	    $this->assign('rate',json_encode($rate));
	    $this->assign('prize_name',json_encode($prize_name));
	    $this->assign('prize',$prize);
	    $this->assign('prize_people',$prize_people);
	    $this->assign('prize_active',$prize_active);
		return $this->fetch('index');
	}

	public function lucky(){
	    $user_id = $this->userId;
	    $prize_id = $this->request->get('prize_id', '');
	    $active_id = $this->request->get('active_id', '');
        $prize = Db::name('signup_prize')->find($prize_id);
        $prize['stock'] = $prize['stock'] - 1;
        if($prize['stock'] == 0){
            $prize['rate'] = 0;
        }
        Db::name('signup_prize')->update($prize);
        $data = array(
            'prize_active_id' => $active_id,
            'user_id' => $user_id,
            'create_time' => date("Y-m-d H-i-s"),
            'name' => $prize['name'],
            'content' => $prize['content'],
            'img_url' => $prize['img_url'],
        );
        Db::name('signup_prize_user')->insert($data);
        echo 1;
    }


}
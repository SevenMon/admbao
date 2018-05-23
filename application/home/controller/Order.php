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

class Order extends Base {

	public function addOrder(){
		$user_id = $this->userId;

        $compete_id = $this->request->get('id', '');
        $data_notice = Db::name('signup_compete_notice')->where(array('compete_id' => $compete_id))->find();
        $data = Db::name('signup_compete')->find($compete_id);
        if (!$this->request->isPost()) {
			$user_info = getUserAllInfo($user_id);
			$this->assign('user_info',$user_info);
            $this->assign('id',$compete_id);
            $this->assign('data',$data);
            $this->assign('data_notice',$data_notice);
            return $this->fetch('index');
            exit();
        }else{
            //var_dump($_REQUEST);
            $order = array(
                'order_num' => time().rand(1000,9999),
                'price' => $data['price'],
                'type' => 0,
                'user_id' => $this->userId,
                'compete_id' => $compete_id,
                'people_num' => 1,
                'extra_info' => json_encode(array(
                    'data' => $data,
                    'data_notice' => $data_notice
                )),
                'attachment' => '',
                'create_time' => date("Y-m-d H-i-s"),
                'status' => 0,
            );

            $order_id = Db::name('signup_order')->insertGetId($order);
            Db::name('signup_compete')->where(array('id' => $data['id']))->update(array('sell_num' => ($data['sell_num'] + 1)));

            $order_people = array(
                'order_id' => $order_id,
                'name' => $_POST['name'],
                'phone' => $_POST['phone'],
                'sex' => $_POST['sex'],
                'birth' => $_POST['birth'],
                'id_card' => $_POST['id_card'],
                'country' => $_POST['country'],
                'email' => $_POST['email'],
                'address' => $_POST['address'],
                'company' => $_POST['company'],
                'size' => $_POST['size'],
                'color' => $_POST['color'],
                'emergency_person' => $_POST['emergency_person'],
                'emergency_phone' => $_POST['emergency_phone'],
                'car_address' => $_POST['car_address'],
                'receive_good_address' => $_POST['receive_good_address'],
                'create_time' => date("Y-m-d H-i-s")
            );
            Db::name('signup_order_people_info')->insert($order_people);
            return $this->fetch('pay');
        }

    }
}
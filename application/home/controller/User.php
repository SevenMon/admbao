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

class User extends Controller {
	public function edit(){

		if (!$this->request->isPost()) {
			$userId = $_GET['userId'];
			$user_info = getUserAllInfo($userId);
			$this->assign('user_info',$user_info);
			return $this->fetch('edit');
		}else{
			$user_info = getUserAllInfo($_POST['user_id']);
			$status = $user_info['user_info']['status'];
			$data = array(
				'id' => $_POST['user_id'],
				'phone' => $_POST['phone'],
				'name' => $_POST['name'],
				'sex' => $_POST['sex'],
				'birth' => $_POST['birth'],
				'card_id' => $_POST['card_id'],
				'card_type' => $_POST['card_type'],
				'email' => $_POST['email'],
				'country' => $_POST['country'],
				'status' => 1,
			);
			$info = Db::name('signup_user')->update($data);
			if($status == 0){
				//首页
				$this->redirect('/home');
			}else{
				//个人中心页面
				$this->redirect('/home/Index/user');
			}
		}
    }
}
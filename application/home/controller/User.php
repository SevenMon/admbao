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
	public function downSignupTable(){
		//$file_name = "signup";
		$file_name = "signup_table.csv";     //下载文件名
		$file_dir = "static/file/";        //下载文件存放目录
		//检查文件是否存在
		if (! file_exists ( $file_dir . $file_name )) {
			header('HTTP/1.1 404 NOT FOUND');
		} else {
			//以只读和二进制模式打开文件
			$file = fopen ( $file_dir . $file_name, "rb" );
			//告诉浏览器这是一个文件流格式的文件
			Header ( "Content-type: application/octet-stream" );
			//请求范围的度量单位
			Header ( "Accept-Ranges: bytes" );
			//Content-Length是指定包含于请求或响应中数据的字节长度
			Header ( "Accept-Length: " . filesize ( $file_dir . $file_name ) );
			//用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
			Header ( "Content-Disposition: attachment; filename=" . $file_name );
			//读取文件内容并直接输出到浏览器
			//echo fread ( $file, filesize ( $file_dir . $file_name ) );
			//fclose ( $file );
			readfile($file_dir . $file_name);
			exit ();
		}
	}
}
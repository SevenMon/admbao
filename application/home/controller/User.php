<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/16
 * Time: 10:06
 */
namespace app\home\controller;

use think\Controller;

class User extends Controller {
	public function index(){
		return $this->fetch('index');
	}
	public function edit(){
        return $this->fetch('edit');
    }
}
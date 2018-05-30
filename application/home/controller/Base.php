<?php
namespace app\home\controller;
use think\Controller;
use think\Db;
use app\common\Wechat;
use think\facade\Config;

class Base extends Controller {
    protected $user = '';
    protected $userId = '';
    /*
     * 檢驗用戶是否已經登錄過
     */
    public function __construct()
    {
        parent::__construct();
        $this->userId = 12;
		/*$openid = session('openid');
        $wechat_info = Db::name('wechat_fans')->where(array('openid' => $openid))->find();
        if($wechat_info){
            $user_info = Db::name('signup_user')->where(array('wechat_id' => $wechat_info['id']))->find();
        	if($user_info['status'] == 0){
				header("Location: " . Config::get('common.uri') . "home/User/edit?userId=".$user_info['id']);
        	}
            $this->user = $user_info;
            $this->userId = $user_info['id'];
        }else{
            $we = new Wechat();
            $url = $we::CALLBACKURL;
            $go = $we->getOAuthRedirect($url);
			header("Location: ".$go);
        }*/
    }
}
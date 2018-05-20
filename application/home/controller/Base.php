<?php
namespace app\home\controller;

use think\Controller;
use think\Db;
use Wechat\Wechat;

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

        /*$this->userId = session('userId');
        $openid = session('openid');
        $wechat_info = Db::name('wechat_fans')->where(array('open_id' => $openid))->find();
        if($openid && $wechat_info){
            $user_info = Db::name('signup_user')->where(array('wechat_id' => $wechat_info['id']))->find();
            $this->user = $user_info;
            $this->userId = $user_info['id'];
        }else{
            $we = new \Wechat\Wechat();
            $url = $we::CALLBACKURL;
            $go = $we->getOAuthRedirect($url);
            redirect($go);
        }*/

    }
}
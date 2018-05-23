<?php
namespace app\home\controller;

use think\Controller;
use app\common\Wechat;
use think\Db;

class Weixin extends Controller
{
    public function index()
    {
        echo "index";
    }

    /*
         * 获取用户信息
         * 将openid存入session
         */
    public function callback()
    {
		
        $we = new Wechat();
        $token = $we->getOauthAccessToken();
        $accessToken = $we->getToken();
        if($token){
            $user = $we->getOauthUserInfo($accessToken,$token['openid']);
			if($user){
				unset($user['groupid']);
				unset($user['tagid_list']);
				$info = Db::name('wechat_fans')->where(array('openid' => $user['openid']))->find();
				if($info){
					$update_info = Db::name('wechat_fans')->where(array('id' => $info['id']))->update($user);
					$watch_id = $update_info['id'];
				}else{
					$watch_id = Db::name('wechat_fans')->insert($user);
					Db::name('signup_user')->insert(array('wechat_id' => $watch_id));
				}
				session('openid',$user['openid']);
				$user_info = Db::name('signup_user')->insert(array('wechat_id' => $watch_id));
				$user_watch_info = Db::name('wechat_fans')->where(array('openid' => $user['openid']))->find();

				if($user_info['status'] == 0){
					$backUrl = "http://home.lovewh.cn/home/User/edit"; //要跳转的url
				}else{
					$backUrl = "http://home.lovewh.cn"; //要跳转的url
				}
			}else{
				$this->error('获取用户信息失败');
			}
        }else{
            $this->error('获取accesstoken失败');
        }
    }
    
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        if ($this->checkSignature()) {
            echo $echoStr;
            exit;
        }
    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        //extract post data
        if (!empty($postStr)) {

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Content><![CDATA[%s]]></Content>
                        <FuncFlag>0</FuncFlag>
                        </xml>";
            if (!empty($keyword)) {
                $msgType = "text";
                $contentStr = "Welcome to wechat world!";
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
            } else {
                echo "Input something...";
            }
        } else {
            echo "";
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = "laundry";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
}
<?php
namespace app\home\controller;

use think\Controller;
use app\common\Wechat;

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
			$myfile = fopen("testfile.txt", "w");
			fwrite($myfile, json_encode($user));
			exit();
            /*if($user){
                $userId = D('WeixinUser')->getInfoByOpenId($user['openid'],array('userId'));
                if($userId){
                    D('WeixinUser')->updateWeixinUser($userId['userId'],$user);        //更新微信信息，如果有修改昵称或者头像
                    session('openid',$user['openid']);
                    $backUrl = "http://home.lovewh.cn"; //要跳转的url
                    redirect($backUrl);
                }else{
                    if($uid = D('WeixinUser')->addWeixin($user)) {
                        //送优惠券
                        $couponInfo = D('Coupon')->find(1);
                        if(!empty($couponInfo) &&  $couponInfo['isSend'] == 1){
                            D('UserCoupon')->sendCoupon($uid,1);
                        }

                        session('openid',$user['openid']);
                        $backUrl = "http://home.lovewh.cn"; //要跳转的url
                        redirect($backUrl);
                    }else{
                        $this->error('token数据库写入失败');
                    }
                }
            }else{
                $this->error('获取用户信息失败');
            }*/
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
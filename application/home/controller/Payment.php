<?php
namespace app\home\controller;

use think\Controller;
use think\facade\Config;

class Payment extends Controller
{
    /*
     * 支付
     */
    public function callback(){
		
        /*$orderId = I('orderId');
        if($orderId > 10000000 && $orderId <= 50000000){
            //普通订单
            $orderInfo = D('Order')->find($orderId);
        }elseif($orderId > 50000000){
            //充值订单
            $orderInfo = D('WalletOrder')->find($orderId);
        }else{
            $this->error("错误的订单号".$orderId);
        }
        if(empty($orderInfo)){
            $this->error("错误的订单号".$orderId);
        }else{
            $price = $orderInfo['price'];
            $orderId = $orderInfo['orderId'];
        }*/

        $price = bcmul(0.1,100);
        ini_set('date.timezone','Asia/Shanghai');
        require_once $_SERVER['DOCUMENT_ROOT']."/application/common/WeixinPay/lib/WxPay.Api.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/application/common/WeixinPay/example/WxPay.JsApiPay.php";
        $tools = new \JsApiPay();
		
        $openId = $tools->GetOpenid();
        $input = new \WxPayUnifiedOrder();

        $input->SetBody("微信支付");
        $input->SetAttach("微信公众号支付");
		
        $input->SetOut_trade_no("1312342");//订单号
        $input->SetTotal_fee("1");//总金额
		
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("订单");
        $input->SetNotify_url(Config::get('common.uri')."home/Payment/callback");//回调地址
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
		
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $this->assign('jsApiParameters',$jsApiParameters);
        return $this->fetch('payment');
    }

    public function payment(){
    	echo 123;
    	exit();
        $xmlStr = file_get_contents("php://input");
        $xml = simplexml_load_string($xmlStr);
        $attach = $this->unicodeDecode((string) $xml->attach);//商家数据包，原样返回
        $mch_id = $this->unicodeDecode((string) $xml->mch_id);//微信支付分配的商户号
        $openid = $this->unicodeDecode((string) $xml->openid);//用户在商户appid下的唯一标识
        $out_trade_no = $this->unicodeDecode((string) $xml->out_trade_no);//商户系统的订单号，与请求一致。
        $result_code = $this->unicodeDecode((string) $xml->result_code);//SUCCESS/FAIL
        $return_code = $this->unicodeDecode((string) $xml->return_code);//错误返回的信息描述
        $time_end = $this->unicodeDecode((string) $xml->time_end);//支付时间
        $total_fee = $this->unicodeDecode((string) $xml->total_fee);//支付金额
        $transaction_id = $this->unicodeDecode((string) $xml->transaction_id);//微信订单号
        $sign = $this->unicodeDecode((string) $xml->sign);//签名
        $callInfo = array(
            'attach' => $attach,
            'mch_id' => $mch_id,
            'openid' => $openid,
            'out_trade_no' => $out_trade_no,
            'result_code' => $result_code,
            'return_code' => $return_code,
            'time_end' => $time_end,
            'total_fee' => $total_fee,
            'transaction_id' => $transaction_id,
        );
        error_log(print_r(json_encode($callInfo),true),3,'/mnt/logs/bee.log');
        if ($return_code == 'SUCCESS') {
            $logWeixinPay = D('LogWeixinPay');
            //TODO 增加签名验证
            //if ($beeCloud->checkSign($appId, $appSecret, $msg['timestamp'], $msg['sign'])) {
            if(1){
                $ret = $logWeixinPay->callback($callInfo);
            } else {
                $ret = 'sign error';
            }
        } else {
            $ret = 'No msg';
        }
        if($ret!='success'){
            error_log(date('Y-m-d H:i:s')."===============================\n",3,'/mnt/logs/bee.log');
            error_log(print_r($ret,true),3,'/mnt/logs/bee.log');
            error_log($ret."\n",3,'/mnt/logs/bee.log');
        }
        echo $ret;
        exit;
    }
    function unicodeDecode($name){
        $json = '{"str":"'.$name.'"}';
        $arr = json_decode($json,true);
        if(empty($arr)) return '';
        return $arr['str'];
    }


}
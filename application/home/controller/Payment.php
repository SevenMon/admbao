<?php
namespace app\home\controller;

use think\Controller;
use think\facade\Config;
use think\Db;

class Payment extends Controller
{
    /*
     * 支付
     */
    public function callback(){
		$order_id = $_GET['order_id'];
		$order_info = Db::name('signup_order')->find($order_id);
		
		if(empty($order_info) || $order_info['status'] == 1){
			return $this->error('订单数据错误！','/home/Index/index');
		}

        ini_set('date.timezone','Asia/Shanghai');
        require_once $_SERVER['DOCUMENT_ROOT']."/application/common/WeixinPay/lib/WxPay.Api.php";
        require_once $_SERVER['DOCUMENT_ROOT']."/application/common/WeixinPay/example/WxPay.JsApiPay.php";
        $tools = new \JsApiPay();
		
        $openId = $tools->GetOpenid();
        $input = new \WxPayUnifiedOrder();

        $input->SetBody("微信支付");
        $input->SetAttach("微信公众号支付");
		
        $input->SetOut_trade_no($order_info['order_num']);//订单号
        $input->SetTotal_fee($order_info['price']);//总金额
		
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("订单");
        $input->SetNotify_url(Config::get('common.uri')."home/payment/payment");//回调地址
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
		
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $this->assign('jsApiParameters',$jsApiParameters);
		
        return $this->fetch('payment');
    }

    public function payment(){
		
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
            'sign' => $sign,
        );
		
		/*
		$myfile = fopen("newfile.txt", "a");
		fwrite($myfile, json_encode($callInfo));
		fclose($myfile);
        error_log(print_r(json_encode($callInfo),true),3,'/mnt/logs/bee.log');
		*/
		$myfile = fopen("newfile.txt", "a");
		fwrite($myfile, json_encode($return_code));
		fclose($myfile);
        if ($return_code == 'SUCCESS') {
            $myfile = fopen("newfile1.txt", "a");
		fwrite($myfile, json_encode($return_code));
		fclose($myfile);
			//填写支付成功的日志
			$data = $callInfo;
			unset($data['sign']);
			$myfile = fopen("newfile21.txt", "a");
		fwrite($myfile, json_encode($data));
		fclose($myfile);
			try{
		
				//Db::name('signup_payinfo')->insert($data);
			} catch (HttpResponseException $exception) {
				return $exception->getResponse();
			} catch (\Exception $e) {
				$myfile = fopen("newfile2.txt", "a");
		fwrite($myfile, $this->error($e->getMessage()));
		fclose($myfile);
				$this->error($e->getMessage());
			}
			
			
			
			
            //TODO 增加签名验证
            //if ($beeCloud->checkSign($appId, $appSecret, $msg['timestamp'], $msg['sign'])) {
            $order_info = Db::name('signup_order')->where(array('order_num' => $out_trade_no))->find();
			$myfile = fopen("newfile3.txt", "a");
		fwrite($myfile, Db::name('signup_order')->getLastSql());
		fclose($myfile);
			if(!empty($order_info) && $order_info['status'] == 0){
				Db::name('signup_order')->where(array('order_num' => $out_trade_no))->update(array('status' => 1));
				$myfile = fopen("newfile4.txt", "a");
		fwrite($myfile, Db::name('signup_order')->getLastSql());
		fclose($myfile);
			}elseif(!empty($order_info) && $order_info['status'] == 1){
				$myfile = fopen("newfile5.txt", "a");
		fwrite($myfile, '234');
		fclose($myfile);
				$ret = 'success';
			}else{
				$myfile = fopen("newfile6.txt", "a");
		fwrite($myfile, '234');
		fclose($myfile);
			}
        } else {
            $ret = 'No msg';
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
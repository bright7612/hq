<?php
/**
 * Created by PhpStorm.
 * User: Stone
 * Date: 2016/9/19
 * Time: 00:12
 * mail: wxstones@gmail.com
 */
namespace Ljz\Controller;
use Think\Controller;
Vendor('Wxpay.WxPay#Api');
Vendor('Wxpay.WxPay#JsApiPay');
class WxpayController extends Controller {
	/** 链接wechat接口 */
    public function index(){
        if(I("post.sum")){
            session("wx_sum",I("post.sum"));
        }
        $moeny = session("wx_sum");
		$moeny = 0.01;
        $tal_fee = $moeny * 100;

        //①、获取用户openid
        $tools = new \JsApiPay();
        $openId = $tools->GetOpenid();
        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("爱护助贝充值");
        $input->SetAttach("爱护助贝充值");
        $input->SetOut_trade_no(\WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee($tal_fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("爱护助贝充值");
        $input->SetNotify_url("http://care.wiseljz.com/home/Wxpay/notify.html");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $this->assign('jsApiParameters',$jsApiParameters);
        $this->assign('moeny',$moeny);
        $this->display();
    }

	/* 支付回调地址 只要有数据回来  那说明   肯定是支付成功的   不支付成功不会回调*/
	public function notify(){

        $xml = file_get_contents("php://input");
        $data = json_decode(json_encode(simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA)),TRUE);
//		dump($data);
        $data['xml'] = $xml;
        if(!$xml)$xml="";
        $data['create_time'] = time();
//		dump($data);
//		$addlog = M("wxpay_log")->add($data);

        $ordermap['out_trade_no'] =  $data['out_trade_no'];
        $searhorder = M("wxpay_log")->where($ordermap)->find();


        if( $searhorder['out_trade_no']){

        }else{

            $addlog = M("wxpay_log")->add($data);
//			echo M()->getlastsql();

            $orderdata['openId'] =  $data['openid'];

//			echo $_POST['openid'];
            $searchmember = M("ucenter_member")->where($orderdata)->order('id desc')->find();
//			echo M()->getlastsql();
            M('Member')->where('uid='.$searchmember['id'])->setInc('score4',($data['total_fee']/100));
//				scoreLogAdd($searchmember['id'],$_POST['total_fee'],'充值获得'.$_POST['total_fee'].'贝',$addlog,'充值获得贝','inc');
            $datadone['done'] = 1;
            M("wxpay_log")->where('id='.$addlog)->save($datadone);
        }

        $su = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        echo $su;
    }
}
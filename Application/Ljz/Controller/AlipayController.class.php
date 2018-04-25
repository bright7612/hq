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

Vendor('Alipay.Corefunction');
Vendor('Alipay.Rsafunction');
Vendor('Alipay.Notify');


class AlipayController extends Controller {

	public function signatures() {
//		header("Content-type: text/html; charset=utf-8"); 
		//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
		//合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
		$alipay_config['partner']		= '2088611119454042';
		
		//商户的私钥,此处填写原始私钥去头去尾，RSA公私钥生成：https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.nBDxfy&treeId=58&articleId=103242&docType=1
		$alipay_config['private_key']	= 'MIICXwIBAAKBgQDOr6hGky+N17wkpJC4xrZzmM0mqu2WEYqSRKpYoE44duJxQWs1
		xQPf2G0lAOUeWiHH/G/yIrBH+S0iDk9z3JcJ1OLCNOM2/kx/8iCAxnBSoqR5hksx
		maZGOaaYGjOUfuJBuvxTLX5FiRea3h8FCXk9raw/tcGcL3jcqPPeMA3KHQIDAQAB
		AoGBAMNzDLgge3mwprQoAssY7nQF7QpB3QZqdBW5ZTUF8rImih/7cvyX4AAP07aI
		UCIRrZxGNT5OW1DUaz+nNK8lzJTFbRMeWvsGsNg76eQVfrcaYUnj2BUoRjDkxIVC
		JgNQ1g0mmPGZZed5zZ7A+atxkZE4cnK1r/rDhmxnZFD+cftpAkEA9nNQSqqMYwfE
		rkCPvl9dwWVXqSp2jpi40A/o15GfCQeVDZxc3Ia976D0MmTM68TUz12bZ8LigIdU
		qn24E+QlywJBANax5gIfHbc7+o23PsiGBRv3yvj8YDKrbwOrJfepi41k1yOKAzYL
		bZfn4ZspSzHu21C21va74SJcQyBr89obkrcCQQDTCbvftFuzEZvie3ab1p46VcXT
		HoGXakAYKweAUTqWSN/iX9tFHDzZTkLORHMWEd8KE2ZYXBIJbdmahT10CxIxAkEA
		sNM6pnqsRdM/jGLlcdB3+s3+vU1XicQKnhHjJnTcvGrWiq3L8UI+VEOmW94J0alx
		tquwpuydA2jL3LMs13GoRwJBANyw8dutLEUCYOdPcbV7UvfDQ4EAfSLJmH/MYghW
		SQczE9kKy1FOYpSZgoMqyjJq/fa4mDhXzZE/MeYcgfn927c=';
		
		//支付宝的公钥，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
		$alipay_config['alipay_public_key']= 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB';
		//支付宝的公钥，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
		$alipay_config['alipay_public_key']= 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB';
		//商户公钥
		$alipay_config['alipay_wiseljzkey']= 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDOr6hGky+N17wkpJC4xrZzmM0mqu2WEYqSRKpYoE44duJxQWs1xQPf2G0lAOUeWiHH/G/yIrBH+S0iDk9z3JcJ1OLCNOM2/kx/8iCAxnBSoqR5hksxmaZGOaaYGjOUfuJBuvxTLX5FiRea3h8FCXk9raw/tcGcL3jcqPPeMA3KHQIDAQAB';
		
		$alipay_config['alipay_wiseljzkey']='-----BEGIN PUBLIC KEY-----
		MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDOr6hGky+N17wkpJC4xrZzmM0m
		qu2WEYqSRKpYoE44duJxQWs1xQPf2G0lAOUeWiHH/G/yIrBH+S0iDk9z3JcJ1OLC
		NOM2/kx/8iCAxnBSoqR5hksxmaZGOaaYGjOUfuJBuvxTLX5FiRea3h8FCXk9raw/
		tcGcL3jcqPPeMA3KHQIDAQAB
		-----END PUBLIC KEY-----';
		//异步通知接口
		$alipay_config['service']= 'mobile.securitypay.pay';
		//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
		
		//签名方式 不需修改
		$alipay_config['sign_type']    = strtoupper('RSA');
		
		//字符编码格式 目前支持 gbk 或 utf-8
		$alipay_config['input_charset']= strtolower('utf-8');
		
		//ca证书路径地址，用于curl中ssl校验
		//请保证cacert.pem文件在当前文件夹目录中
		$alipay_config['cacert']    = getcwd().'/cacert.pem';
		
		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$alipay_config['transport']    = 'http';

		date_default_timezone_set("PRC");
		
		$orderdata['session'] =  $_POST['session'];
		$searchmember = M("ucenter_member")->where($orderdata)->order('id desc')->find();
		
		if($searchmember['id']){
			$out['out_trade_no'] = "wiseljz".time().randCode(8);
			$orderdata['out_trade_no_create'] = $out['out_trade_no'] ;
			$orderdata['create_time'] = time();
			$orderdata['uid'] = $searchmember['id'];
			$orderdata['session'] = $_POST['session'];
			
			$orderdata['total_fee_create'] = $_POST['session'];
			$orderdata['seller_id_create'] = '2088611119454042';

			M("wisealipay_log")->add($orderdata);
//			echo M()->getlastsql();
		}else{
			$outjson['message']='session错误！';
			$outjson['result']='error';
			echo json_encode($outjson);
			exit;
		}

		if (str_replace('"','',$_POST['partner'])==$alipay_config['partner']&&str_replace('"','',$_POST['service'])==$alipay_config['service']) {
		
			$data = 'partner="2088611119454042"&seller_id="finance@wiseljz.com"&out_trade_no="'.$out['out_trade_no'].'"&subject="'.$_POST['subject'].'"&body="'
			.$_POST['body'].'"&total_fee="'.$_POST['total_fee'].'"&notify_url="http://care.wiseljz.com/home/Alipay/notifyurl.html"&service="mobile.securitypay.pay"&payment_type="1"&_input_charset="utf-8"&it_b_pay="30m"&show_url="m.alipay.com"&app_id="2016101002075409"';

			//将待签名字符串使用私钥签名,且做urlencode. 注意：请求到支付宝只需要做一次urlencode.
			$rsa_sign=urlencode(rsaSign($data, $alipay_config['private_key']));


//			echo $rsa_sign ;exit;
			//把签名得到的sign和签名类型sign_type拼接在待签名字符串后面。
			$data = $data.'&sign='.'"'.$rsa_sign.'"'.'&sign_type='.'"'.$alipay_config['sign_type'].'"';

			//返回给客户端,建议在客户端使用私钥对应的公钥做一次验签，保证不是他人传输。
			$out['result']='success';
			$out['rsaSignedEnd'] = $data;
			$out['message']='验签成功！';

//			header("Content-type: text/html; charset=utf-8"); 
			echo json_encode($out);

		}
		else{
			$outjson['message']='不匹配或为空！';
			$outjson['result']='error';
			echo json_encode($outjson);
			logResult(createLinkstring($_POST));
		}

	}

	/******************************
	 服务器异步通知页面方法
	 其实这里就是将notify_url.php文件中的代码复制过来进行处理

	 *******************************/
	function notifyurl() {

		$alipay_config['partner']		= '2088611119454042';
		$alipay_config['private_key']	= 'MIICXwIBAAKBgQDOr6hGky+N17wkpJC4xrZzmM0mqu2WEYqSRKpYoE44duJxQWs1
		xQPf2G0lAOUeWiHH/G/yIrBH+S0iDk9z3JcJ1OLCNOM2/kx/8iCAxnBSoqR5hksx
		maZGOaaYGjOUfuJBuvxTLX5FiRea3h8FCXk9raw/tcGcL3jcqPPeMA3KHQIDAQAB
		AoGBAMNzDLgge3mwprQoAssY7nQF7QpB3QZqdBW5ZTUF8rImih/7cvyX4AAP07aI
		UCIRrZxGNT5OW1DUaz+nNK8lzJTFbRMeWvsGsNg76eQVfrcaYUnj2BUoRjDkxIVC
		JgNQ1g0mmPGZZed5zZ7A+atxkZE4cnK1r/rDhmxnZFD+cftpAkEA9nNQSqqMYwfE
		rkCPvl9dwWVXqSp2jpi40A/o15GfCQeVDZxc3Ia976D0MmTM68TUz12bZ8LigIdU
		qn24E+QlywJBANax5gIfHbc7+o23PsiGBRv3yvj8YDKrbwOrJfepi41k1yOKAzYL
		bZfn4ZspSzHu21C21va74SJcQyBr89obkrcCQQDTCbvftFuzEZvie3ab1p46VcXT
		HoGXakAYKweAUTqWSN/iX9tFHDzZTkLORHMWEd8KE2ZYXBIJbdmahT10CxIxAkEA
		sNM6pnqsRdM/jGLlcdB3+s3+vU1XicQKnhHjJnTcvGrWiq3L8UI+VEOmW94J0alx
		tquwpuydA2jL3LMs13GoRwJBANyw8dutLEUCYOdPcbV7UvfDQ4EAfSLJmH/MYghW
		SQczE9kKy1FOYpSZgoMqyjJq/fa4mDhXzZE/MeYcgfn927c=';
		$alipay_config['alipay_public_key']= 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB';
		$alipay_config['service']= 'mobile.securitypay.pay';
		$alipay_config['sign_type']    = strtoupper('RSA');
		$alipay_config['input_charset']= strtolower('utf-8');
		$alipay_config['cacert']    = getcwd().'/cacert.pem';
		$alipay_config['transport']    = 'http';
		date_default_timezone_set("PRC");
		$data = $_POST;
		
		$ordermap['out_trade_no_create'] =  $_POST['out_trade_no'];
		$searhorder = M("wisealipay_log")->where($ordermap)->find();
		
		if($searhorder['out_trade_no_create']){
			$data['xml'] = json_encode($_POST);
			M("wisealipay_log")->where($ordermap)->save($data);
//			echo M()->getlastsql();
		}else{
			$data['xml'] = json_encode($_POST);
			if(!$data['xml'])$data['xml']="error";
			$data['create_time'] = time();
			M("wisealipay_log")->add($data);
		}
		
		

//		echo M()->getlastsql();
		
		//计算得出通知验证结果
		$alipayNotify = new \AlipayNotify($alipay_config);
			if($alipayNotify->getResponse($_POST['notify_id']))//判断成功之后使用getResponse方法判断是否是支付宝发来的异步通知。
			{
				if($alipayNotify->getSignVeryfy($_POST, $_POST['sign'])) {//使用支付宝公钥验签
					
					//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
		    		//获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
					//商户订单号
					$out_trade_no = $_POST['out_trade_no'];
		
					//支付宝交易号
					$trade_no = $_POST['trade_no'];
		
					//交易状态
					$trade_status = $_POST['trade_status'];
		
		    		if($_POST['trade_status'] == 'TRADE_FINISHED') {
					//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序
					//注意：
					//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
					//请务必判断请求时的out_trade_no、total_fee、seller_id与通知时获取的out_trade_no、total_fee、seller_id为一致的
		    		}
		    		else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
		
						//贝增操作

					//判断该笔订单是否在商户网站中已经做过处理
					//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
					//如果有做过处理，不执行商户的业务程序
					//注意：
					//付款完成后，支付宝系统发送该交易状态通知
					//请务必判断请求时的out_trade_no、total_fee、seller_id与通知时获取的out_trade_no、total_fee、seller_id为一致的
		    		}
					//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
					
					
					
					echo "success";		//请不要修改或删除
				}
				else //验证签名失败
				{
					echo "sign fail";
				}
			}
			else //验证是否来自支付宝的通知失败
			{
				echo "response fail";
			}
	}



}

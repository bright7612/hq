<?php
function nameHide($name)
{
    $name = msubstr($name, 0, 1, "utf-8", 0) . "**";
    return ($name);
}

function uidgetstar($uid)
{
    $map['receiver_id'] = $uid;
    $map['star'] = array('gt', 0);;
    $out = M("order")->where($map)->avg('star');
    return round($out);
}


function uidGetField($uid,$filed){
    $out = M("member")->where('uid=' . $uid)->field($filed)->find();
    return $out;
}

function uidgetphone($uid)
{
    $out = M("ucenter_member")->where('id=' . $uid)->field('id,mobile')->find();
    return $out['mobile'];
}

function phonegetuid($phone)
{
    $map['mobile'] = $phone;
    $out = M("ucenter_member")->where($map)->field('id,mobile')->find();
    return $out['id'];
}

function uidgetinfo($uid)
{
    $out = M("member")->where('uid=' . $uid)->find();
    return $out;
}

function uidgetface($uid)
{
    $out = M("member")->where('uid=' . $uid)->field('face')->find();
    return $out['face'];
}

function uidGetKeeper($uid)
{
    $out = M("member_guardian")->where("uid=" . $uid)->field("id,gid,gname,gmobile")->select();
    if ($out) {
        foreach ($out as &$v) {
            if ($v['gid'] != 0) {
                $res = M('member')->where('uid=' . $v['gid'])->field('face,community')->find();
                $v['face'] = $res['face'];
                $v['cname'] = idgetcommunityname($res['community']);
            }
        }
    }
    return $out;
}

function uidGetPupil($uid)
{
    $out = M("member_guardian")->where("gid=" . $uid)->field('id,uid,uname,umobile')->select();
    if ($out) {
        foreach ($out as &$v) {
            if ($v['uid'] != 0) {
                $res = M('member')->where('uid=' . $v['uid'])->field('face,community')->find();
                $v['face'] = $res['face'];
                $v['cname'] = idgetcommunityname($res['community']);
            }
        }
    }
    return $out;
}

function uidGetTwinVolunteer($uid)
{
    $out = M("member_twinning")->where("uid=" . $uid)->field('id,tid,tname,tmobile')->select();
    if ($out) {
        foreach ($out as &$v) {
            if ($v['tid'] != 0) {
                $res = M('member')->where('uid=' . $v['tid'])->field('face,community')->find();
                $v['face'] = $res['face'];
                $v['cname'] = idgetcommunityname($res['community']);
            }
        }
    }
    return $out;
}

function uidGetTwinVolunteerIds($uid)
{
    $out = M("member_twinning")->where("uid=" . $uid)->field('tid')->select();
    $res = array();
    if ($out) {
        foreach ($out as &$v) {
            if ($v['tid'] != 0) {
                $res[] =$v['tid'];
            }
        }
    }
    return $res;
}

function uidGetTwinCaller($uid)
{
    $out = M("member_twinning")->where("tid=" . $uid)->field('id,uid,uname,umobile')->select();
    if ($out) {
        foreach ($out as &$v) {
            if ($v['uid'] != 0) {
                $res = M('member')->where('uid=' . $v['uid'])->field('face,community')->find();
                $v['face'] = $res['face'];
                $v['cname'] = idgetcommunityname($res['community']);
            }
        }
    }
    return $out;
}



function uidGetKeeperCount($uid)
{
    return M("member_guardian")->where('uid=' . $uid)->count();
}

function uidGetPupilCount($uid)
{
    return M("member_guardian")->where('gid=' . $uid)->count();
}

function uidGetTwinVCount($uid)
{
    return M("member_twinning")->where('uid=' . $uid)->count();
}

function uidGetTwinCCount($uid)
{
    return M("member_twinning")->where('tid=' . $uid)->count();
}

function uidgetname($uid)
{
    $out = M("member")->where('uid=' . $uid)->field('realName')->find();
    return $out['realName'];
}

function serviceTypeGetName($str)
{

    $arr = explode(',', $str);
    $namearr = M("service_type")->select();
    foreach ($namearr as $k => $v) {
        $arrout[$k] = $v['name'];
    }

    for ($i = 0; $i < count($arr); $i++) {
        $out[$i]['id'] = $arr[$i];
        $out[$i]['name'] = $arrout[$i];
    }

    return $out;
}

function oidGetOrder($oid)
{
    $out = M("order")->where('id=' . $oid)->find();
    return $out;
}

function uidgetaddress($uid)
{
    $out = M("address")->where('uid=' . $uid)->order('id desc')->find();
    return $out;
}

function uidGetAddressCount($uid)
{
    return M("address")->where('uid=' . $uid)->count();
}

function istwin($uid,$tid){
    if (M("member_twinning")->where(array("tid"=>$tid,"uid"=>$uid))->find()){
        return true;
    }else{
        return false;
    }
}

function idgetcommunity($id)
{
    $out = M("community")->where('id=' . $id)->order('id desc')->find();
    return $out;
}

function idgetcommunityname($id)
{
    $out = M("community")->where('id=' . $id)->field("name")->find();
    return $out['name'];
}

function isMobileNew($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}

function apiResault($result, $message, $extra = array())
{
    $out = array('result' => $result, 'message' => strval($message));
    $out = array_merge($out, $extra);
    return json_encode($out);
}

function getFace($face)
{
    if ($face) {
        return C('SERVER_IP') . $face;
    } else {
        return "/Public/images/ic_avatar.png";
    }
}

/**
 * 模拟post进行url请求
 * @param string $url
 * @param array $post_data
 */
function request_post($url = '', $post_data = array())
{
    if (empty($url)) {
        return false;
    }

    $o = "";
    foreach ($post_data as $k => $v) {
        $o .= "$k=" . urlencode($v) . "&";
    }
    $post_data = substr($o, 0, -1);

    $postUrl = $url;
    $curlPost = $post_data;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL, $postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);

    return $data;
}


function smsace($mobile = '', $msg = '')
{

    $check = "CF#6eTNvcgS8iKZ5!sF0foeh45eDYGDB";
    $time = time();
    $key = md5($time . $check);
//	$msg='您好，您的验证码是888888asdj健康萨达夸奖了第三方第三方看空间里发生地方科技楼发生的进口量放大思考就浪费大谁看见了丰盛的空间裂缝圣诞节快乐飞圣诞节快乐仿盛大';
    $info_url = 'http://iptv.wiseljz.com/sms/sent.php';

    $info_url .= "?mobile=" . $mobile;
    $info_url .= "&msg=" . urlencode($msg);
    $info_url .= "&time=" . $time;
    $info_url .= "&key=" . $key;

    $info_data = httpnew($info_url);
    if ($info_data[0] == 200) {
//		$list = json_decode($info_data[1], TRUE);
        $list = $info_data[1];
    }
//	echo $info_url;exit;
    return 1;

}


function httpnew($url, $method, $postfields = null, $headers = array(), $debug = FALSE)
{
    $ci = curl_init();
    /* Curl settings */
    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ci, CURLOPT_TIMEOUT, 30);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);

    switch ($method) {
        case 'POST' :
            curl_setopt($ci, CURLOPT_POST, true);
            if (!empty($postfields)) {
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
                $postdata = $postfields;
            }
            break;
    }
    curl_setopt($ci, CURLOPT_URL, $url);
    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ci, CURLINFO_HEADER_OUT, true);

    $response = curl_exec($ci);
    $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);

    if ($debug) {
        echo "=====post data======\r\n";
        var_dump($postfields);

        echo '=====info=====' . "\r\n";
        print_r(curl_getinfo($ci));

        echo '=====$response=====' . "\r\n";
        print_r($response);
    }
    curl_close($ci);
    return array($http_code, $response);
}

function randCode($length = 5, $type = 0)
{
    $arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
    if ($type == 0) {
        array_pop($arr);
        $string = implode("", $arr);
    } else if ($type == "-1") {
        $string = implode("", $arr);
    } else {
        $string = $arr[$type];
    }
    $count = strlen($string) - 1;
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $str[$i] = $string[rand(0, $count)];
        $code .= $str[$i];
    }
    return $code;
}


function idGetAllInfo($uid)
{
    $userInfosession = M("ucenter_member")->field("session")->where("id=" . $uid)->find();
    $userInfo['obj'] = M("member")->field("uid,realName,idCard,community,education,political,job,Lat,Lng,face,score1,signature,permission,assessment,serviceTimes,flags,sendflags,serviceType,lv,ccp,skills,score4")->where("uid=" . $uid)->find();
    $userInfo['obj']['address'] = M("address")->where('uid=' . $uid)->select();
    $userInfo['obj']['session'] = $userInfosession['session'];
    $userInfo['obj']['mobile'] = M("ucenter_member")->field("mobile")->where("id=" . $uid)->find()['mobile'];
    if ($userInfo['obj'] ['community']) {
        $userInfoObjCommunity = M("community")->where('id=' . $userInfo['obj'] ['community'])->field("id,name,address,phone")->find();
        if ($userInfoObjCommunity) {
            $userInfo['obj']['community'] = $userInfoObjCommunity;
        }
    }
    if ($userInfo['obj']['community'] == 0) {
        $userInfo['obj']['community'] = array();
        unset($userInfo['obj']['community']);
    }
    $userInfo['obj']['role'] = M("user_role")->where('uid=' . $uid)->field('role_id')->order("role_id desc")->limit(0, 1)->select();
    $checkgroup['role'] = $userInfo['obj']['role'][0]['role_id'];
    $checkrole1 = M("ucenter_member")->where("id=" . $uid)->save($checkgroup);
    $checkrole1 = M("member")->where("uid=" . $uid)->save($checkgroup);

    if ($checkgroup['role'] == 3) {
        $userInfo['obj']['twinning_elder'] = M("member_twinning")->where('tid=' . $uid)->field('id,uid,uname,umobile')->select();
    }
    if ($checkgroup['role'] == 4) {
        $userInfo['obj']['twinning_volunteer'] = M("member_twinning")->where('uid=' . $uid)->field('id,tid,tname,tmobile')->select();
        $userInfo['obj']['guardian'] = M("member_guardian")->where('uid=' . $uid)->field('id,gid,gname,gmobile')->select();
    }
    if ($checkgroup['role'] == 5) {
        $userInfo['obj']['pupillus'] = M("member_guardian")->where('gid=' . $uid)->field('id,uid,uname,umobile')->select();
    }

    return $userInfo;
}

function newSession($uid)
{
    $data['session'] = md5($uid . time());
    $dosession = M("ucenter_member")->where("id=" . $uid)->save($data);
}

function formatStatus($order)
{
    $status = $order['status'];
    $res = "";
    switch ($status) {
        case 0:
            $res = "已取消";
            break;
        case 1:
            $res = "等待接单";
            break;
        case 2:
            $res = "已被接单";
            break;
        case 3:
            $res = "志愿者已完成";
            break;
        case 4:
            if($order['comment'])
            {
                $res = "已评价";
            }else{
                $res = "已完成，待评价";
            }
            break;
        case 5:
            $res = "超时自动取消";
            break;
        case 6:
            $res = "已完成";//"已送锦旗"
            break;
    }
    return $res;
}

function formatStatus3($order)
{
    $status = $order['status'];
    $res = "";
    switch ($status) {
        case 0:
            $res = "已取消";
            break;
        case 1:
            $res = "等待接单";
            break;
        case 2:
            $res = "进行中";
            break;
        case 3:
            $res = "进行中";
            break;
        case 4:
            $res = "已完成";
            break;
        case 5:
            $res = "超时自动取消";
            break;
        case 6:
            $res = "已送礼物";//"已送锦旗"
            break;
    }
    return $res;
}
function formatStatus4($order)
{
    $status = $order['status'];
    $res = "";
    switch ($status) {
        case 0:
            $res = "已取消";
            break;
        case 1:
            $res = "等待接单";
            break;
        case 2:
            $res = "进行中";
            break;
        case 3:
            $res = "进行中";
            break;
        case 4:
            $res = "已完成";
            break;
        case 5:
            $res = "超时自动取消";
            break;
        case 6:
            $res = "收到礼物";
            break;
    }
    return $res;
}

function formatStatus2($order)
{
    $status = $order['status'];
    $res = "";
    switch ($status) {
        case 0:
            $res = "已取消";
            break;
        case 1:
            $res = "等待接单";
            break;
        case 2:
            $res = "支助中";
            break;
        case 3:
            $res = "志愿者已完成";
            break;
        case 4:
            if($order['comment'])
            {
                $res = "已评价";
            }else{
                $res = "已完成，待评价";
            }
            break;
        case 5:
            $res = "超时自动取消";
            break;
        case 6:
            $res = "已完成";//"已送锦旗"
            break;
    }
    return $res;
}


function formatServiceType($id)
{
    if( is_numeric($id) ){
        $res = M('service_type')->where("id=" . $id)->field("name")->find();
        if($res) {
            return $res['name'];
        }
    }
    return "";
}


// 以POST方式提交数据
function post_data($url, $param, $is_file = false, $return_array = true) {
	if (! $is_file && is_array ( $param )) {
		$param = JSON ( $param );
	}
	if ($is_file) {
		$header [] = "content-type: multipart/form-data; charset=UTF-8";
	} else {
		$header [] = "content-type: application/json; charset=UTF-8";
	}
	
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
	curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
	curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
	curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $param );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	$res = curl_exec ( $ch );
	
	$flat = curl_errno ( $ch );
	if ($flat) {
		$data = curl_error ( $ch );
		addWeixinLog ( $flat, 'post_data flat' );
		addWeixinLog ( $data, 'post_data msg' );
	}
	
	curl_close ( $ch );
	
	$return_array && $res = json_decode ( $res, true );
	
	return $res;
}
function JSON($array) {
	arrayRecursive ( $array, 'urlencode', true );
	$json = json_encode ( $array );
	return urldecode ( $json );
}

/**
 * ************************************************************
 *
 * 使用特定function对数组中所有元素做处理
 *
 * @param
 *        	string &$array 要处理的字符串
 * @param string $function
 *        	要执行的函数
 * @return boolean $apply_to_keys_also 是否也应用到key上
 * @access public
 *        
 *         ***********************************************************
 */
function arrayRecursive(&$array, $function, $apply_to_keys_also = false) {
	static $recursive_counter = 0;
	if (++ $recursive_counter > 1000) {
		die ( 'possible deep recursion attack' );
	}
	foreach ( $array as $key => $value ) {
		if (is_array ( $value )) {
			arrayRecursive ( $array [$key], $function, $apply_to_keys_also );
		} else {
			$array [$key] = $function ( $value );
		}
		
		if ($apply_to_keys_also && is_string ( $key )) {
			$new_key = $function ( $key );
			if ($new_key != $key) {
				$array [$new_key] = $array [$key];
				unset ( $array [$key] );
			}
		}
	}
	$recursive_counter --;
}


/* 点击授权 */
function StoneOauth(){
	if(empty($_GET['code'])){
		$appid = 'wxb997ab9195650adb';
		$redirect_uri = urlencode(HttpCul());
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=stone#wechat_redirect';
		header('Location:'.$url);
	}elseif(!empty($_GET['code'])){
		$appid = 'wxb997ab9195650adb';
		$secret = '8a26a3722665e96d9fd16e52431e1e2b';
		$code = $_GET['code'];
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
		$data = json_decode(file_get_contents($url),true);
	}
	if(!empty($data['access_token'])){
		$url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$data['access_token'].'&openid='.$data['openid'].'&lang=zh_CN';
		$openid = json_decode(file_get_contents($url),true);
		return $openid;
	}
}

/* 静默授权 */
function OauthStone(){
	if(empty($_GET['code'])){
		$appid = 'wxb997ab9195650adb';
		$redirect_uri = urlencode(HttpCul());
		$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_base&state=stone#wechat_redirect';
		header('Location:'.$url);
	}else{
		$appid = 'wxb997ab9195650adb';
		$secret = '8a26a3722665e96d9fd16e52431e1e2b';
		$code = $_GET['code'];
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
		$openid = json_decode(file_get_contents($url),true);
		return $openid['openid'];
	}
}


/*  HTTP 请求  post/get*/
function http($url,$data,$type){
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	if($type == 'POST'){
		curl_setopt($ch,CURLOPT_POST,1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
	}
	curl_setopt($ch,CURLOPT_HEADER,0);
	curl_setopt($ch,CURLOPT_TIMEOUT,30);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

function HttpCul(){
	$url = "http://";
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
		$url = "https://";
	}
	if($_SERVER['SERVER_PORT'] != '80'){
		$url .= $_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
	}else{
		$url .= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
	return $url;
}


//////////////////////////////////////////////////////
//Orderlist数据表，用于保存用户的购买订单记录；
/* Orderlist数据表结构；
CREATE TABLE `tb_orderlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,购买者userid
  `username` varchar(255) DEFAULT NULL,购买者姓名
  `ordid` varchar(255) DEFAULT NULL,订单号
  `ordtime` int(11) DEFAULT NULL,订单时间
  `productid` int(11) DEFAULT NULL,产品ID
  `ordtitle` varchar(255) DEFAULT NULL,订单标题
  `ordbuynum` int(11) DEFAULT '0',购买数量
  `ordprice` float(10,2) DEFAULT '0.00',产品单价
  `ordfee` float(10,2) DEFAULT '0.00',订单总金额
  `ordstatus` int(11) DEFAULT '0',订单状态
  `payment_type` varchar(255) DEFAULT NULL,支付类型
  `payment_trade_no` varchar(255) DEFAULT NULL,支付接口交易号
  `payment_trade_status` varchar(255) DEFAULT NULL,支付接口返回的交易状态
  `payment_notify_id` varchar(255) DEFAULT NULL,
  `payment_notify_time` varchar(255) DEFAULT NULL,
  `payment_buyer_email` varchar(255) DEFAULT NULL,
  `ordcode` varchar(255) DEFAULT NULL,       //这个字段不需要的，大家看我西面的修正补充部分的说明！
  `isused` int(11) DEFAULT '0',
  `usetime` int(11) DEFAULT NULL,
  `checkuser` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

*/
//在线交易订单支付处理函数
//函数功能：根据支付接口传回的数据判断该订单是否已经支付成功；
//返回值：如果订单已经成功支付，返回true，否则返回false；
function checkorderstatus($ordid){
    $Ord=M('Orderlist');
    $ordstatus=$Ord->where('ordid='.$ordid)->getField('ordstatus');
    if($ordstatus==1){
        return true;
    }else{
        return false;    
    }
}

//处理订单函数
//更新订单状态，写入订单支付后返回的数据
function orderhandle($parameter){
    $ordid=$parameter['out_trade_no'];
    $data['payment_trade_no']      =$parameter['trade_no'];
    $data['payment_trade_status']  =$parameter['trade_status'];
    $data['payment_notify_id']     =$parameter['notify_id'];
    $data['payment_notify_time']   =$parameter['notify_time'];
    $data['payment_buyer_email']   =$parameter['buyer_email'];
    $data['ordstatus']             =1;
    $Ord=M('Orderlist');
    $Ord->where('ordid='.$ordid)->save($data);
} 



/*-----------------------------------
2013.8.13更正
下面这个函数，其实不需要，大家可以把他删掉，
具体看我下面的修正补充部分的说明
------------------------------------*/

//获取一个随机且唯一的订单号；
function getordcode(){
    $Ord=M('Orderlist');
    $numbers = range (10,99);
    shuffle ($numbers); 
    $code=array_slice($numbers,0,4); 
    $ordcode=$code[0].$code[1].$code[2].$code[3];
    $oldcode=$Ord->where("ordcode='".$ordcode."'")->getField('ordcode');
    if($oldcode){
        getordcode();
    }else{
        return $ordcode;
    }
}
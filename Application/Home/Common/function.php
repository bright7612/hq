<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */



/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
 
function think_ucenter_md5($str, $key = 'ThinkUCenter')
{
    return '' === $str ? '' : md5(sha1($str) . $key);
}
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}
function clean00($str){
    $str = str_replace('00:00', '', $str);
    return $str;
}
function statename($state,$status){
	if($state==0) return '已申请！';
	if($state==1) return '已审核通过！';
	if($state==2) return '审核未通过！';
    if($state==3) return '已取消！';
}

//
//function httpsms($url, $method, $postfields = null, $headers = array(), $debug = FALSE)
//{
//    $ci = curl_init();
//    /* Curl settings */
//    curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
//    curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
//    curl_setopt($ci, CURLOPT_TIMEOUT, 30);
//    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
//
//    switch ($method) {
//        case 'POST' :
//            curl_setopt($ci, CURLOPT_POST, true);
//            if (!empty($postfields)) {
//                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
//                $postdata = $postfields;
//            }
//            break;
//    }
//    curl_setopt($ci, CURLOPT_URL, $url);
//    curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
//    curl_setopt($ci, CURLINFO_HEADER_OUT, true);
//
//    $response = curl_exec($ci);
//    $http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
//
//    if ($debug) {
//        echo "=====post data======\r\n";
//        var_dump($postfields);
//
//        echo '=====info=====' . "\r\n";
//        print_r(curl_getinfo($ci));
//
//        echo '=====$response=====' . "\r\n";
//        print_r($response);
//    }
//    curl_close($ci);
//    return array($http_code, $response);
//}

//function smsljz($mobile = '', $msg = '')
//{
//
//    $check = "CF#6eTNvcgS8iKZ5!sF0foeh45eDYGDB";
//    $time = time();
//    $key = md5($time . $check);
////	$msg='您好，您的验证码是888888asdj健康萨达夸奖了第三方第三方看空间里发生地方科技楼发生的进口量放大思考就浪费大谁看见了丰盛的空间裂缝圣诞节快乐飞圣诞节快乐仿盛大';
//    $info_url = 'http://iptv.wiseljz.com/sms/sent.php';
//
//    $info_url .= "?mobile=" . $mobile;
//    $info_url .= "&msg=" . urlencode($msg);
//    $info_url .= "&time=" . $time;
//    $info_url .= "&key=" . $key;
//
//    $info_data = httpsms($info_url);
//    if ($info_data[0] == 200) {
////		$list = json_decode($info_data[1], TRUE);
//        $list = $info_data[1];
//    }
////	echo $info_url;exit;
//    return 1;
//
//}

function geoEncode($addr,$city="上海市"){
    $ak = "DQ1BXIF0qGFddSG8wudhSHCZeftt7ztz";
    $addr = urlencode($addr);
    $city = urlencode($city);
    $url = "http://api.map.baidu.com/geocoder/v2/?address=$addr&city=$city&output=json&ak=$ak";
    $res =httpGet($url);
    $res = json_decode($res,true);
    if($res['status'] == 0){
        return   $res['result']['location'];
    }
    return false;
}

function httpGet($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 2000);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}
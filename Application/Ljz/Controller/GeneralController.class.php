<?php
/**
 * Created by PhpStorm.
 * User: l.wang
 * Date: 2016/8/26
 * Time: 15:15
 */

namespace Ljz\Controller;


use Think\Controller;

/**
 * Class CommonController  所有人通用界面放在这里
 * @package Home\Controller
 */
class GeneralController extends CommonController
{
    /**
     * home志愿者列表页( 求助者/监护人共用 )
     */
    public function home()
    {
        //$signPackage = $this->getSignPackage();
        $info = session('info');
        if(!$info['community'] || !$info['realname'])
        {
            $this->redirect('completeInfo');
        } else {
            $this->assign('community', $info['community']['id']);
            $this->display();
        }
    }

    public function listVolunteer($order = 0, $page = 1, $rows = 10)
    {
        $p["session"] = session("session");
        $p["page"] = $page;
        $p["rows"] = $rows;
        $p["order"] = $order;
        $p["community"] = session("info")["community"]["id"];
        $res = request_post(C('SERVER_IP') . '/api/wx/wall', $p);
        $res = json_decode($res, true);
        if (!$res['list']) {
            echo "nomore";
            exit;
        }
        $res = $res['list'];


        $i = 1;
        foreach ($res as &$v) {
            $v["index"] = 10 * ($page - 1) + $i;
            $i++;
            if (!empty($v["serviceType"])) {
                $stypes =explode(",", $v["serviceType"]);
                $stypes1 = array();
                foreach ($stypes as $v1){
                    if(is_numeric($v1) && sizeof($stypes1)<3){
                        $stypes1[]=$v1;
                    }
                }
                $v['stypes'] = $stypes1;
            }
        }

        $twinV = uidGetTwinVolunteer(session('uid'));
        $twinVIds = array();
        if($twinV){
            foreach ($twinV as $va){
                $twinVIds[]=$va['tid'];
            }
        }
        //dump($res);
        $this->assign("twinVids",implode(",",$twinVIds));
        $this->assign("vlist", $res);
        $this->display();
    }

    public function completeInfo(){
        $this->assign("sign",$this->getSignPackage());
        $c = request_post(C('SERVER_IP') . '/api/member/community');
        $this->assign('communities', json_decode($c, true)['obj']);
        $this->assign("mycommunity", session('info')['community']);
        $this->assign('info',idGetAllInfo(session('uid'))['obj']);
        $this->display();
    }


    public function pupil()
    {
        $this->assign('info', session('info'));
        $this->assign('pupil', uidGetPupil(session('uid')));
        $this->display();
    }

    /**
     * home求助页，附近的志愿者( 求助者/监护人共用 )
     */
    public function hresort()
    {
//        $info = uidgetinfo($uid);
//        $Lat = $info['lat'];
//        $Lng = $info['lng'];
//        if (!$Lat || !$Lng) {
//            echo apiResault(1801, "经纬度有误");
//            exit;
//        }
//        $start = ($page - 1) * $rows;
//        $res = M()->query("SELECT uid,realName,Lat,Lng,face, ROUND(6378.138*2*ASIN(SQRT(POW(SIN((" . $Lat . "*PI()/180-lat*PI()/180)/2),2)+COS(" . $Lat . "*PI()/180)*COS(lat*PI()/180)*POW(SIN((" . $Lng . "*PI()/180-lng*PI()/180)/2),2)))*1000) AS juli FROM ws_member  where community = '" . $info['community'] . "' and  ROUND(6378.138*2*ASIN(SQRT(POW(SIN((" . $Lat . "*PI()/180-lat*PI()/180)/2),2)+COS(" . $Lat . "*PI()/180)*COS(lat*PI()/180)*POW(SIN((" . $Lng . "*PI()/180-lng*PI()/180)/2),2)))*1000) <20000 ORDER BY juli asc  LIMIT " . $start . "," . $rows);

//		dump($res);
        $this->assign('info',uidgetinfo(session('uid')));
        $this->display();
    }

    /**
     *
     */
    public function getNearby($uid = 120, $lat = 0, $lng = 0, $page = 1, $rows = 20)
    {
        $info = uidgetinfo($uid);
        if (!$lat || !$lng) {
            echo apiResault(1801, "经纬度有误");
            exit;
        }
        $start = ($page - 1) * $rows;
        $res = M()->query("SELECT uid,realName,Lat,Lng,face, ROUND(6378.138*2*ASIN(SQRT(POW(SIN((" . $lat . "*PI()/180-lat*PI()/180)/2),2)+COS(" . $lat . "*PI()/180)*COS(lat*PI()/180)*POW(SIN((" . $lng . "*PI()/180-lng*PI()/180)/2),2)))*1000) AS juli FROM ws_member  where community = '" . $info['community'] . "' and  ROUND(6378.138*2*ASIN(SQRT(POW(SIN((" . $lat . "*PI()/180-lat*PI()/180)/2),2)+COS(" . $lat . "*PI()/180)*COS(lat*PI()/180)*POW(SIN((" . $lng . "*PI()/180-lng*PI()/180)/2),2)))*1000) <20000 ORDER BY juli asc  LIMIT " . $start . "," . $rows);
        echo json_encode($res);
        exit;
    }

    /**
     * home我的页面( 求助者/监护人共用 ,界面有区分)
     */
    public function hmine()
    {
        if(!session("uid"))
        {
            $this->redirect("Index/binding");
            exit;
        }
        $info = idGetAllInfo(session('uid'))['obj'];
        $info['score4'] = round($info['score4'],2);
        $this->assign('info', $info);
        $this->display();
    }
    /**
     * home我的页面( 求助者/监护人共用 ,界面有区分)
     */
    public function hstyle()
    {
        if(!session("uid"))
        {
            $this->redirect("Index/binding");
            exit;
        }
        $this->display();
    }
    /**
     * home我的页面( 求助者/监护人共用 ,界面有区分)
     */
    public function hwish()
    {
        if(!session("uid"))
        {
            $this->redirect("Index/binding");
            exit;
        }
        $this->display();
    }

    /**
     * 积分页面，所有人共用
     */
    public function integral()
    {
        $this->assign('info', idGetAllInfo(session("uid"))['obj']);
        $this->display();
    }

    public function listIntegral($page = 1, $rows = 10)
    {
        $map['uid'] = session('uid');
        $map['value'] = array("neq", 0);
        $start = ($page - 1) * $rows;
        $res = D('score_log')->where($map)->limit($start, $rows)->order("id desc")->select();
        if (!$res) {
            echo "nomore";
            exit;
        }

        foreach ($res as $k => $v) {
            if ($v['fs_id']) $res[$k]['fs_face'] = uidgetface($v['fs_id']);
            if ($v['receiver_id']) $res[$k]['receiver_face'] = uidgetface($v['receiver_id']);
        }
        $this->assign("list", $res);
        $this->display();
    }

    /**
     * 贝
     */
    public function bei()
    {
        $info = idGetAllInfo(session("uid"))['obj'];
        $info['score4'] = round($info['score4'],2);
        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 我送出的锦旗
     */
    public function pennantSend()
    {
        $this->display();
    }

    public function listPennantSend($page = 1, $rows = 10)
    {
        $p['uid'] = session('uid');
        $p['page'] = $page;
        $p['rows'] = $rows;
        $res = request_post(C('SERVER_IP') . '/api/wx/myflags', $p);
        $res = json_decode($res, true);
        if (!$res['listgive']) {
            echo 'nomore';
            exit;
        }
        $this->assign("list", $res["listgive"]);
        $this->display();
    }

    /**
     * 我送出的锦旗
     */
    public function pennantGet()
    {
        $this->assign("info", session('info'));
        $this->display();
    }

    public function listPennantGet($page = 1, $rows = 10)
    {
        $p['uid'] = session('uid');
        $p['page'] = $page;
        $p['rows'] = $rows;
        $res = request_post(C('SERVER_IP') . '/api/wx/myflags', $p);
        $res = json_decode($res, true);
        if (!$res['listget']) {
            echo 'nomore';
            exit;
        }
        $this->assign("list", $res["listget"]);
        $this->display();
    }


    /**
     * 求助页(选择求助项) (可以求助的人共用)
     */
    public function resort()
    {
        $tovid = I('get.tovid');
        if ($tovid) { //向某人求助
            $vinfo = uidgetinfo($tovid);
            $types = M("service_type")->where("id in (".$vinfo['servicetype'].")")->select();
            $this->assign("tovid", $tovid);
            $this->assign("types", $types);
        } else { //向所有人求助
            $pTypes =M('service_type')->where(array("type"=>1,"pid"=>0))->select();
            foreach ($pTypes as &$v)
            {
                $v['cTypes'] = M('service_type')->where(array("type"=>1,"pid"=>$v['id']))->select();
            }
            $this->assign("pTypes",$pTypes);
        }
        $this->display();
    }

    /**
     * 求助页(选择求助项) (可以求助的人共用)
     */
    public function msgresort($tovid="")
    {
        $this->assign("sign", $this->getSignPackage());
        $this->assign("tovid", $tovid);
        $this->display();
    }

    /**
     * 评价志愿者 (可以求助的人共用)
     */
    public function evaluatev($oid, $uid)
    {
        //$res = uidgetinfo($uid);
        $p["session"] = session('session');
        $p["uid"] = $uid;
        $res = request_post(C('SERVER_IP') . '/api/member/info', $p);
        $res = json_decode($res,true);
        $this->assign("vinfo", $res['obj']);
        $this->assign("oid", $oid);
        $this->display();
    }

    /**
     * 送锦旗页面 (可以送锦旗者共用)
     */
    public function sendpennant($oid)
    {
        $oinfo = oidGetOrder($oid);
        $this->assign("oinfo",$oinfo);
        $this->display();
    }

    /**
     * 志愿者主页信息 (可以查看志愿者信息者共用,如果是其他志愿者查看，就不显示求助信息)
     */
    public function vhomepage($uid)
    {
        $vinfo = uidgetinfo($uid);
//        $map['receiver_id'] = $uid;
//        $map['fs_id'] = $uid;
//        $list = M("order")->where($map)->select();
        $stypes = explode(',', $vinfo['servicetype']);
        $this->assign('vinfo', $vinfo);
        $this->assign('role',session('role'));
        $this->assign("istwin",istwin(session('uid'),$uid));
        $this->assign('canresort',I('get.canresort'));
        if($stypes[0]) {
            $this->assign('stypes', $stypes);
        }
        $this->assign("from",session('from'));
        $this->display();
    }

    /**
     * 被监护人主页信息
     */
    public function chomepage($uid)
    {
        $cinfo = uidgetinfo($uid);
        $this->assign('cinfo', $cinfo);
        $this->display();
    }


    public function listVHomeOrder($page=1, $rows=10,$uid,$status=10)
    {
        $p['session'] = session('session');
        $p['page'] = $page;
        $p['rows'] = $rows;
        $p['status'] = $status;
        $p['type']=I('get.type');
        $p['uid']=$uid;
        $res = request_post(C('SERVER_IP') . '/api/member/myHelpList', $p);
        $res = json_decode($res, true);
        if (!$res['list']) {
            echo "nomore";
            exit;
        }
        $this->assign("list", $res['list']);
        $this->display();
    }
    public function listCHomeOrder($page=1, $rows=10,$uid,$status=10)
    {
        $p['session'] = session('session');
        $p['page'] = $page;
        $p['rows'] = $rows;
        $p['status'] = $status;
        $p['type']=I('get.type');
        $p['uid']=$uid;
        $res = request_post(C('SERVER_IP') . '/api/member/myOrderList', $p);
        $res = json_decode($res, true);
        if (!$res['list']) {
            echo "nomore";
            exit;
        }
        //dump($res);
        $this->assign("list", $res['list']);
        $this->display();
    }

    /**
     * 我的求助列表页 (可以求助者共用)
     */
    public function myresort($type)
    {
//        $map['fs_id'] = $uid;
//        $list = M("order")->where($map)->select();
        $this->assign('type', $type);
        $this->display();
    }

    public function listOrder($page = 1, $rows = 10, $status = 9)
    {
        $p['session'] = session('session');
        $p['page'] = $page;
        $p['rows'] = $rows;
        $p['status'] = $status;
        $p['type'] = I('get.type');
        $res = request_post(C('SERVER_IP') . '/api/member/myOrderList', $p);
        $res = json_decode($res, true);
        if (!$res['list']) {
            echo "nomore";
            exit;
        }

        $this->assign("list", $res['list']);
        $this->display();
    }

    /**
     * 订单详情页 (所有人共用)
     */
    public function orderdetail($id)
    {
        $p['session'] = session('session');
        $p['oid'] = $id;
        $res = request_post(C('SERVER_IP') . '/api/member/orderInfo', $p);
//        dump(json_decode($res, true));
//        exit;
        $res = json_decode($res, true);
        $this->assign("order", $res['info']);
        $this->assign("giftInfo", $res['giftInfo']);
        $this->display();
    }

    /**
     * 个人信息页，所有角色都有，各有不同
     */
    public function info()
    {
        $this->assign("sign",$this->getSignPackage());
        $this->assign("info", uidgetinfo(session("uid")));
        //dump(uidgetinfo(session("uid")));
        $this->assign("role", session("role"));
        $this->assign("from",session("from"));
        $this->display();
    }

    /**
     * 编辑个人信息
     */
    public function editinfo()
    {
        $this->assign("info", uidgetinfo(session("uid")));
        $this->display();
    }
    /**
     * 编辑姓名
     */
    public function editname()
    {
        $this->assign("info", uidgetinfo(session("uid")));
        $this->display();
    }
    /**
     * 编辑学历
     */
    public function editeducation()
    {
        $this->assign("info", uidgetinfo(session("uid")));
        $this->display();
    }
    /**
     * 编辑职业
     */
    public function editjob()
    {
        $this->assign("info", uidgetinfo(session("uid")));
        $this->display();
    }
    /**
     * 编辑特长与爱好
     */
    public function editskills()
    {
        $this->assign("info", uidgetinfo(session("uid")));
        $this->display();
    }
    /**
     * 编辑自我介绍
     */
    public function editintroduce()
    {
        $info = uidgetinfo(session('uid'));
        $this->assign("info", $info);
        $this->display();
    }
    /**
     * 编辑家庭住址
     */
    public function homeaddress()
    {
        $this->assign("info", uidgetinfo(session("uid")));
        $this->display();
    }
    public function keeper()
    {
        $this->assign("info", session('info'));
        $this->assign("keeper", uidGetKeeper(session('uid')));
        $this->display();
    }

//    public function deleteKeeper($uid,$gid)
//    {
//        $res = M('member_guardian')->where(array("uid"=>$uid,"gid"=>$gid))->delete();
//        if($res >=1 ) {
//            echo apiResault(1, "删除成功");
//        }else{
//            echo apiResault(-1, "删除失败");
//        }
//    }


    /**
     * 常用地址列表页
     */
    public function addresses()
    {
        $this->assign('info', session('info'));
        //$this->assign("address",session('info')['address']);
        $this->display();
    }

    /**
     * 添加常用地址
     */
    public function addaddress()
    {
        $this->display();
    }

    /**
     * 选择社区
     */
    public function selectcommunity()
    {
        $c = request_post(C('SERVER_IP') . '/api/member/community');
        $this->assign('communities', json_decode($c, true)['obj']);
        $this->assign("mycommunity", session('info')['community']);
        $this->display();
    }

    /**
     * 设置可以提供的服务的标签
     */
    public function tag()
    {
        $types = uidGetField(session('uid'),'serviceType')['servicetype'];//自己的服务类型
        $tagsin = array();
        if($types)
        {
            $tagsin =M('service_type')->where("id in (".$types.")")->select();
        }

        $this->assign('myservicetypes',$types);
        $this->assign("tagsin", $tagsin);

        $pTypes =M('service_type')->where(array("type"=>1,"pid"=>0))->select();
        foreach ($pTypes as &$v)
        {
            $v['cTypes'] = M('service_type')->where(array("type"=>1,"pid"=>$v['id']))->select();
        }
        $this->assign("pTypes",$pTypes);
        $this->display();
    }

    public function twincaller(){
        $this->assign('info', session('info'));
        $this->assign('twin', uidGetTwinCaller(session('uid')));
        $this->display();
    }

    public function twinvolunteer(){
        $this->assign('info', session('info'));
        $this->assign('twin', uidGetTwinVolunteer(session('uid')));
        $this->display();
    }

    public function  authVolunteer(){
        $info =  idGetAllInfo(session('uid'))['obj'];
        $this->assign("sign",$this->getSignPackage());
        $this->assign('info', $info);
        $this->assign("sign",$this->getSignPackage());
        $c = request_post(C('SERVER_IP') . '/api/member/community');
        $this->assign('communities', json_decode($c, true)['obj']);
        $this->assign("mycommunity", session('info')['community']);

//        $res = M('service_type')->select();
//
//        $myservicetypes = $info['servicetype'];
//        $myservicetypes = explode(",", $myservicetypes);
//        $in = array();
//        $inId =array();
//        $inStr = array();
//        $out = array();
//        foreach ($res as $v) {
//            if (in_array($v['id'], $myservicetypes)) {
//                $in[] = $v;
//                $inId[] = $v['id'];
//                $inStr[] = $v['name'];
//            } else {
//                $out[] = $v;
//            }
//        }
//        $this->assign("tagsin", $in);
//        $this->assign("tagsout", $out);
//        $this->assign("tagsinIdStr",implode(",",$inId));
//        $this->assign("tagsinStr",implode(",",$inStr));

        $types = uidGetField(session('uid'),'serviceType')['servicetype'];//自己的服务类型
        $tagsin = array();
        if($types)
        {
            $tagsin =M('service_type')->where("id in (".$types.")")->select();
        }
        $this->assign('myservicetypes',$types);
        $this->assign("tagsin", $tagsin);
        $inStr = array();
        foreach ($tagsin as $tv)
        {
            $inStr[] = $tv['name'];
        }
        $this->assign("tagsinStr",implode(",",$inStr));

        $pTypes =M('service_type')->where(array("type"=>1,"pid"=>0))->select();
        foreach ($pTypes as &$v)
        {
            $v['cTypes'] = M('service_type')->where(array("type"=>1,"pid"=>$v['id']))->select();
        }
        $this->assign("pTypes",$pTypes);//所有服务类型

        $this->display();
    }

    public function settings(){
        $this->assign('receivePush',uidGetField(session('uid'),"receivePush")["receivepush"]);
        $this->display();
    }
    public function sendgift($oid){
        if(!$oid)
        {
            echo "送礼物error";
            exit;
        }
        $giftResult =json_decode(request_post(C('SERVER_IP') . '/api/pay/gifts'),true);
        if($giftResult["result"]!="1")
        {
            echo "获取礼物列表失败".$giftResult["message"];
            exit;
        }
        $this->assign('gifts',$giftResult["list"]);
        $this->assign('order',oidGetOrder($oid));
        $this->display();
    }

    public function crop()
    {
        $this->display();
    }

    public function test(){
        $info = cookie('userInfo');
        $image = new \Think\Image();
        $content = file_get_contents(substr($info['headimgurl'],0,-1)."64");
        file_put_contents('./Application/Home/avatar.jpg', $content);
        $image->open('./Application/Home/avatar.jpg');
       // $image->thumb(80, 80,\Think\Image::IMAGE_THUMB_FIXED)->save('./Application/Home/thumb.jpg');
       $image->open('./a.jpg') ->water("./Application/Home/avatar.jpg",array(100,100),100)->text($info['nickname'],'./simhei.ttf',25,'#ff0000',array(200,150))->save("./Application/Home/water.jpg");
        $this->display();
    }
}
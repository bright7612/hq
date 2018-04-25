<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/23
 * Time: 20:00
 */

namespace Ljz\Controller;


use Think\Controller;

class VolunteerController extends Controller
{
    /**
     * home正在求助的人列表页
     */
    public function home()
    {
        $this->display();
    }

    public function listCaller($page = 1, $rows = 10, $otype = 0, $type = 0, $status = 1)
    {
        $p["session"] = session("session");
        $p["status"] = $status;
        $p["page"] = $page;
        $p["rows"] = $rows;
        $p['otype'] = $otype;//0全部 1紧急求助 2 陪伴
        $p['type'] = $type;//不填默认0 按时间排序 ,1 按距离排序
        $res = request_post(C('SERVER_IP') . '/api/member/orderList', $p);
        $res = json_decode($res, true);
        if (!$res['list']) {
            echo "nomore";
            exit;
        }
        $this->assign("list", $res['list']);
        $this->display();
    }

    public function listCaller2($page = 1, $rows = 10, $otype = 0, $type = 0, $status = 1)
    {
        $p["session"] = session("session");
        $p["status"] = $status;
        $p["page"] = $page;
        $p["rows"] = $rows;
        $p['otype'] = $otype;//0全部 1紧急求助 2 陪伴
        $p['type'] = $type;//不填默认0 按时间排序 ,1 按距离排序
        $res = request_post(C('SERVER_IP') . '/api/member/orderList', $p);
        $res = json_decode($res, true);
        if (!$res['list']) {
            echo "nomore";
            exit;
        }
        $this->assign("list", $res['list']);
        $this->display();
    }
    /**
     * home排行榜
     */
    public function hrank()
    {
        $this->display();
    }

    public function listWall($page = 1, $rows = 10, $order = 0)
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
        $this->assign("list", $res);
        $this->display();
    }


    public function listWall2($page = 1, $rows = 10, $order = 0)
    {
        $p["session"] = session("session");
        $p["page"] = $page;
        $p["rows"] = $rows;
        $p["order"] = $order;
        $p["community"] = session("info")["community"]["id"];
        if(!$p["community"])
        {
            $p["community"] = session("communityId");
        }
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

        $this->assign("list", $res);
        $this->display();
    }


    /**
     * home我的（志愿者）
     */
    public function hmine()
    {
        $this->assign("info", idGetAllInfo(session('uid'))['obj']);
        $this->display();
    }

    public function myhelp()
    {
        $this->assign('type', I('get.type'));
        $this->display();
    }

    public function listOrder($page = 1, $rows = 10, $status = 9)
    {
        $p['session'] = session('session');
        $p['page'] = $page;
        $p['rows'] = $rows;
        $p['status'] = $status;
        $p['type'] = I('get.type');
        $res = request_post(C('SERVER_IP') . '/api/member/myHelpList', $p);
        $res = json_decode($res, true);
        if (!$res['list']) {
            echo "nomore";
            exit;
        }
        $this->assign("list", $res['list']);
        $this->display();
    }

    /**
     * 我收到的锦旗
     */
    public function pennant($page = 1, $rows = 20)
    {
//        $start = ($page - 1) * $rows;
//        $map['getUid'] = session('uid');
//        $res = M('gift_log')->where($map)->limit($start, $rows)->order("id desc")->select();
//        dump($res);
        $this->assign("info", session('info'));
        $this->display();
    }

    /**
     * 求助詳情信息
     */
    public function resortinfo($oid = 1)
    {
        //$list = M("order")->where('id=' . $oid)->find();

        $this->display();
    }

    /**
     * 评价求助者
     */
    public function evaluate($oid)
    {
        $oinfo = M("order")->where('id=' . $oid)->find();
        $m = M('member')->where("uid=" . $oinfo['fs_id'])->find();
        $oinfo["fs_signature"] = $m['signature'];
        $oinfo["fs_face"] = $m['face'];
        $this->assign('oinfo', $oinfo);
        $this->display();
    }

    public function orderdetail($id)
    {
        $p['session'] = session('session');
        $p['oid'] = $id;
        $res = request_post(C('SERVER_IP') . '/api/member/orderInfo', $p);
        $res = json_decode($res, true);
        $this->assign("order", $res['info']);
        $this->assign("giftInfo", $res['giftInfo']);
        $this->display();
    }

    /**
     * 志愿者信息，（后面合并到General/info中,现在只是静态显示区别）
     */
    public function info()
    {
        $this->display();
    }


    public function resorting($oid)
    {
        $p['session'] = session('session');
        $p['oid'] = $oid;
        $res = request_post(C('SERVER_IP') . '/api/member/orderInfo', $p);
        $this->assign("order", json_decode($res, true)['info']);
        $this->assign("uid",session('uid'));
        $this->display();
    }
}
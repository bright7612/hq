<?php
/**
 * Created by PhpStorm.
 * User: l.wang
 * Date: 2016/8/30
 * Time: 15:42
 */

namespace Home\Controller;


class ApiController extends CommonController
{
    //deprecated
    public function login($username, $pwd)
    {
        $p["username"] = $username;
        $p["password"] = $pwd;
        $res = request_post(C('SERVER_IP') . '/api/member/login', $p);
        $res = json_decode($res, true);
        if ($res['result'] == "1") {
            session("session", $res['obj']['session']);
            session("info", $res['obj']);
            session("uid", $res['obj']['uid']);
            echo apiResault("1", $res['message']);
        } else {
            echo $res;
        }
    }

    public function infoEdit()
    {
        //修改姓名
        $p['realName'] = I('post.realName');
        //修改签名
        $p['signature'] = I('post.signature');
        //身份证号
        $p['idCard'] = I('post.idCard');
        //角色
        $p['role'] = I('post.role');
        //添加监护人
        $p['guardian'] = I('post.gname');
        $p['guardianMobile'] = I('post.gmobile');
        //添加被监护人
        $p['pupillus'] = I('post.uname');
        $p['pupillusMobile'] = I('post.umobile');
        //修改社区
        $p['community'] = I('post.community');
        //修改服务类型
        $p['serviceType'] = I('post.stypes');
        //头像
        $p['session'] = session('session');
        //添加结对志愿者
        $p["twinningVolunteer"] = I("post.vname");
        $p["twinningVolunteerMobile"] = I("post.vmobile");
        //添加结对老人
        $p["twinningElder"] = I("post.cname");
        $p["twinningElderMobile"] = I("post.cmobile");
        //志愿者认证  2认证中 1通过 0未认证志愿者
        $p["permission"] = I('post.permission');
        //其他服务
        $p["otherServiceType"] = I('post.otherServiceType');
        //是否接受微信推送 1是2否
        $p["receivePush"] = I('post.receivePush');
        $p["education"] = I('post.education');
        $p["job"] = I('post.job');
        $p["skills"] = I('post.skills');
        $p["homeAddress"] = I('post.homeaddress');
        $p["Lng"] = I('post.lng');
        $p["Lat"] = I('post.lat');
        $p["face"] = I('post.face');
        $res = request_post(C('SERVER_IP') . '/api/member/infoEdit', $p);
        if (json_decode($res, true)['result'] == 1) {
            session('info', idGetAllInfo(session("uid"))['obj']);
        }
        echo $res;
    }

    public function faceEdit()
    {
        $p['media_id'] = I('post.serverId');
        $p['session'] = session('session');
        $res = request_post(C('SERVER_IP') . '/api/member/face', $p);
        if (json_decode($res, true)['result'] == 1) {
            session('info', idGetAllInfo(session("uid"))['obj']);
        }
        echo $res;
    }

    public function faceEdit2()
    {
        $p['picstr'] = I('post.picstr');
        $p['session'] = session('session');
        $res = request_post(C('SERVER_IP') . '/api/member/face', $p);
        if (json_decode($res, true)['result'] == 1) {
            session('info', idGetAllInfo(session("uid"))['obj']);
        }
        echo $res;
    }


    public function guardianDel()
    {
        $p['gid'] = I('post.gid');
        $p['session'] = session('session');
        $res = request_post(C('SERVER_IP') . '/api/member/guardianDel', $p);
        if (json_decode($res, true)['result'] == 1) {
            session('info', idGetAllInfo(session("uid"))['obj']);
        }
        echo $res;
    }

    public function twinDel()
    {
        $p['tid'] = I('post.tid');
        $p['session'] = session('session');
        $res = request_post(C('SERVER_IP') . '/api/member/twinningDel', $p);
        if (json_decode($res, true)['result'] == 1) {
            session('info', idGetAllInfo(session("uid"))['obj']);
        }
        echo $res;
    }


    public function addressDel()
    {
        $p['aid'] = I('post.id');
        $p['session'] = session('session');
        $res = request_post(C('SERVER_IP') . '/api/member/addressDel', $p);
        if (json_decode($res, true)['result'] == 1) {
            session('info', idGetAllInfo(session("uid"))['obj']);
        }
        echo $res;
    }

    public function addressAdd()
    {
        $p['session'] = session('session');
        $p['address'] = I('post.address');
        $p['Lat'] = I('post.lat');
        $p['Lng'] = I('post.lng');
        $res = request_post(C('SERVER_IP') . '/api/member/addressAdd', $p);
        if (json_decode($res, true)['result'] == 1) {
            session('info', idGetAllInfo(session("uid"))['obj']);
        }
        echo $res;
    }

    /**
     * 老接口
     */
    public function resort()
    {
        $p['session'] = session('session');
        $p['Lat'] = I('post.lat');
        $p['Lng'] = I('post.lng');
        $p['address'] = I('post.address');
        $p['type'] = I('post.type');
        //$p['accompanyType'] = I('post.accompanyType');
        $p['serviceType'] = I('post.accompanyType');
        $p['content'] = I('post.content');
        $p['media_id'] = I('post.mediaId');
        $res = request_post(C('SERVER_IP') . '/api/member/orderAdd', $p);
        echo $res;
    }

    /**
     * 新接口
     */
    public function resort2()
    {
        $p['session'] = session('session');
        $p['Lat'] = I('post.lat');
        $p['Lng'] = I('post.lng');
        $p['address'] = I('post.address');
        $p['type'] = I('post.type');
        //$p['accompanyType'] = I('post.accompanyType');
        $p['serviceType'] = I('post.accompanyType');
        $p['receiver_id'] = I('post.tovid');
        $p['content'] = I('post.content');
        $p['media_id'] = I('post.mediaId');
        $res = request_post(C('SERVER_IP') . '/api/wx/orderAdd', $p);
        echo $res;
    }

    public function orderDel()
    {
        $p['session'] = session('session');
        $p['oid'] = I('post.id');
        $res = request_post(C('SERVER_IP') . '/api/member/orderDel', $p);
        echo $res;
    }

    public function orderEdit($oid)
    {
        $p['session'] = session('session');
        $p['oid'] = $oid;
        $res = request_post(C('SERVER_IP') . '/api/member/orderEdit', $p);
        echo $res;
    }

    /**志愿者完成订单
     * @param $oid
     * @param $content
     */
    public function complete($oid, $content="")
    {
        $p['session'] = session('session');
        $p['oid'] = $oid;
        $p['result'] = $content;
        $res = request_post(C('SERVER_IP') . '/api/member/orderResult', $p);
        echo $res;
    }


    /**求助者完成
     * @param $oid
     * @param $content
     */
    public function orderComment($oid)
    {
        $p['session'] = session('session');
        $p['oid'] = $oid;
        if(I('post.content')){
            $p['comment'] = I('post.content');
        }
        if(I('post.star')) {
            $p['star'] = I('post.star');
        }
        $res = request_post(C('SERVER_IP') . '/api/member/orderComment', $p);
        echo $res;
    }

    /**
     * 送锦旗
     * @param $oid
     * @param $uid
     * @param $gid
     */
    public function sendPennant($oid, $uid, $gid)
    {
        $p['session'] = session('session');
        $p['oid'] = $oid;
        $p['uid'] = $uid;
        $p['gid'] = $gid;
        $res = request_post(C('SERVER_IP') . '/api/member/orderFlag', $p);
        echo $res;
    }

    /**
     * 修改密码
     */
    public function pwd($oldpwd, $newpwd)
    {
        $p['password'] = $newpwd;
        $p['oldPassword'] = $oldpwd;
        $p['session'] = session('session');
        $res = request_post(C('SERVER_IP') . '/api/member/editPassWord', $p);
        echo $res;
    }

    public function currentOrder()
    {
        $p['session'] = session('session');
        echo request_post(C('SERVER_IP') . '/api/member/orderInfo', $p);
    }


    public function orderInfo($oid)
    {
        $p['session'] = session('session');
        $p["oid"] = $oid;
        echo request_post(C('SERVER_IP') . '/api/member/orderInfo', $p);
    }

    public function myFlags($page = 1, $rows = 10)
    {
        $p['uid'] = session('uid');
        $p['page'] = $page;
        $p['rows'] = $rows;
        echo request_post(C('SERVER_IP') . '/api/wx/myflags', $p);
    }

    public function nearbyInfo()
    {
        $p['session'] = session('session');
        echo request_post(C('SERVER_IP') . '/api/member/nearbyInfo', $p);
    }

    public function sendGift($oid, $uid, $gid)
    {
        $p['session'] = session('session');
        $p['oid'] = $oid;
        $p['uid'] = $uid;
        $p['gid'] = $gid;
        echo request_post(C('SERVER_IP') . '/api/pay/sentGift', $p);
    }

    public function mygift($page = 1, $rows = 10)
    {
        $p['uid'] = session("uid");
        $p['page'] = $page;
        $p['rows'] = $rows;
        echo request_post(C('SERVER_IP') . '/api/pay/myGift', $p);
    }
}
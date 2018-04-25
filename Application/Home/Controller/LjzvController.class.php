<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Think\Controller;


/**
 * 合庆竖屏
 */
class LjzvController extends Controller
{
    public function index()
    {

        $this->display();
    }

    /**
     * 场地预约登记
     */
    public function appoint()
    {
        $map["status"] = 1;
        $map["type"] = 6;
        //$map["id"] = array("gt",79);
        $list = M("data_hdyg")->where($map)->order("sort desc")->select();
        $this->assign("data",$list);
//        $map2["status"] = 1;
//        $map2["type"] = 7;
//        $list2 = M("data_hdyg")->where($map2)->select();
//        $this->assign("data2",$list2);
        $this->display("siteappoint");
    }

    /**
     * 获取活动设备
     * @param $id
     */
    public function getSb($id){
        $sb = M("data_hdsb")->where(array("status" => 1, "aid" => $id))->select();
        $res["status"] = 1;
        $res["data"] = $sb;
        echo json_encode($res);
        exit;
    }


//    public function transData(){
////        header("Content-Type: text/html;charset=utf-8");
////        $list = M("data_zyqd")->where(array("status"=>1,"type"=>3))->select();
////        foreach ($list as $v){
////            $data["name"] = $v["name"];
////            $data["addr"] = $v['addr'];
////            $data["count"] = $v['num'];
////            $data["tel"] = $v['tel'];
////            $data["type"] = 6;//场地预约
////            M("data_hdyg")->add($data);
////        }
//
//        header("Content-Type: text/html;charset=utf-8");
//        $list = M("data_yyhd")->where(array("status"=>1))->select();
//        foreach ($list as $v){
//            $data["datestr"] = $v["datestr"];
//            $data["name"] = $v['name'];
//            $data["content"] = $v['content'];
//            $data["addr"] = $v['addr'];
//            $data["type"] = 7;//56月活动
//            M("data_hdyg")->add($data);
//        }
//    }

    /**
     *  场地预约
     */
    public function hdbm()
    {
        if (IS_POST) {
            $data = $_POST;
            header('Content-Type:text/html; charset=utf-8');
            $data['create_time'] = time();
//            $data['openid'] = cookie('wxOpenId');
            $map['mobile'] = $data['mobile'];

            if (!preg_match("/^1[34578]{1}\d{9}$/", $data['mobile'])) {
                $this->error('请输入正确的手机号！');
                exit;
            }

            $map['create_time'] = array("gt", time() - 5);
            $search = M("data_hdbm")->where($map)->find();
            if ($search['id']) {
                $this->error('此手机短时间内不能再预约了，请耐心等待！', 'zzgx');
                exit;
            }

            //$map3["openid"] = cookie('wxOpenId');
            $map3['mobile'] = $data['mobile'];
            $map3["status"] = 1;
            $map3["aid"] = $data["aid"];
            $map3["state"] = array("in", array(0, 1));
            $map3["starttime"] = array("gt", time());
            $yy = M("data_hdbm")->where($map3)->select();
            unset($map3);
            if ($yy) {
                $this->error('您已有一个相同场地的预约，不能重复预约！');
                exit;
            }


            $data["starttime"] = strtotime($data["start"]);
            $data["endtime"] = strtotime($data["end"]);
            if ($data['name'] && $data['mobile'] && $data['aid'] && $data["starttime"] && $data["endtime"]
                && $data["count"] && $data["company"] && $data["content"]
            ) {
                $mapx['id'] = $data['aid'];
                $datainfo = M("data_hdyg")->where($mapx)->find();
                if ($datainfo['type'] != 6) {
                    $map1["aid"] = $data['aid'];
                    $map1["status"] = 1;
                    $applied = M("data_hdbm")->where($map1)->count();
                    if ($applied >= $datainfo['count']) {
                        $this->error('预约已满，请选择其他活动！', 'hdyg');
                        exit;
                    }
                } else {
                    //场地预约    判断时间段是否合规，

                    //$time1 为3个工作日
                    $time1 = strtotime(date("Y-m-d"));
                    $i = 0;
                    while ($i < 3) {
                        $w = date("w", $time1);
                        if ($w != 6 && $w != 0) {
                            $i++;
                        }
                        $time1 += 3600 * 24;
                    }

                    $time2 = $time1 + 45 * 3600 * 24;
                    if ($data["starttime"] < $time1 || $data["starttime"] > strtotime(date("Y-m-d", time() + 52 * 3600 * 24))) {
                        $this->error('只能预约' . date("Y年m月d日", $time1) . '到' . date("Y年m月d日", $time2) . '之间的日期！', 'hdyg');
                        exit;
                    }


                    if ($data["starttime"] >= $data["endtime"]) {
                        $this->error('活动开始时间不能晚于结束时间！', 'hdyg');
                        exit;
                    }

                    if (date("Y-m-d", $data["starttime"]) != date("Y-m-d", $data["endtime"])) {
                        $this->error('活动开始和结束只能在同一天!');
                        exit;
                    }

                    $map2["status"] = 1;
                    $map2["aid"] = $data['aid'];
                    $map2["state"] = 1;
                    $map2["_string"] = "(starttime <= " . $data["starttime"] . "  and  endtime >= " . $data["starttime"] . ")  or (starttime <=  " . $data['endtime'] . " and endtime >= " . $data['endtime'] . ")  or  (" . $data["starttime"] . " < starttime  and " . $data["endtime"] . "> endtime  )";
                    $applied = M("data_hdbm")->where($map2)->count();
                    if ($applied > 0) {
                        $this->error('此时段已被预约，请选择其他时间，谢谢！');
                        exit;
                    }
                }
                $adddata = M("data_hdbm")->add($data);
                if ($adddata) {
                    if ($datainfo['type'] == 6) {
                        //smsljz($data['mobile'], $msg = '您好:' . $data['name'] . ',活动场地：' . $datainfo['name'] . ' 后台已收到您的预约，审核结果会通过短信告知，请注意查收。谢谢！');
                        $this->success('后台已收到您的预约，审核结果会通过短信告知，请注意查收。谢谢！');
                    } else {
                        smsljz($data['mobile'], $msg = '您好:' . $data['name'] . ',活动：' . $datainfo['name'] . ' 预约成功！ 联系电话：68871363');
                        $this->success('活动预约成功！ 联系电话：68871363');
                    }

                } else {
                    $this->error('数据异常,请稍后再试！');
                }
            } else {
                $this->error('参数有误,请填写完整！');
            }
            exit;
        }
    }

    public function arrive()
    {
        $map["status"] = 1;
        $map["sbd"] = 1;
        $list = M("party_member")->where($map)->group("dzz")->field("dzz,count(*)")->select();
        $dzzcount = array_combine(array_column($list,"dzz"),array_column($list,"count(*)"));
        $map2["id"]= array("in",array_column($list,"dzz"));
        $list2 = M("dzz")->where($map2)->select();
        foreach ($list2 as $k=>$v ){
            $list2[$k]["sbdcount"] =$dzzcount[$v["id"]];
        }
        $this->assign("data",$list2);
        $this->display();
    }

    public function arrive2(){
        $dzzid = I("id",0,"intval");
        $info = M("dzz")->find($dzzid);
        $info["sbdcount"] = M("party_member")->where(array("status"=>1,"sbd"=>1,"dzz"=>$dzzid))->count();
        $this->assign("data",$info);
        $this->display();
    }


    public function golden()
    {

        $this->display();
    }

    public function innovate()
    {

        $this->display();
    }

    public function server()
    {
        $map["status"] = 1;
        $list = M("data_fw")->where($map)->select();
        $this->assign("data",$list);
        $this->display();
    }

    public function volunteer()
    {

        $this->display();
    }

    public function siteappoint()
    {
        $this->display();
    }

    public function demand()
    {
        if (IS_POST) {
            $data = $_POST;
            header('Content-Type:text/html; charset=utf-8');
            $data['create_time'] = time();
//            $data['openid'] = cookie('wxOpenId');

            $data["enddate"] = strtotime($data["enddate"]);
            $data["age"] = intval($data["age"], 0);
            if ($data["xqf"] == 1) {
                if ($data["name"] && $data["enddate"] && $data["xqlb"] && $data["tel"] && $data["sex"] && $data["age"] && $data["content"]) {
                    $res = M("data_xq")->add($data);
                    if ($res) {
                        $this->success('需求提交成功', 'index');
                    } else {
                        $this->error('数据异常,请稍后再试！');
                    }
                } else {
                    $this->error('参数有误,请填写完整！');
                }

            } elseif ($data["xqf"] == 2) {
                if ($data["name"] && $data["enddate"] && $data["xqlb"] && $data["tel"] && $data["dw"] && $data["content"]) {
                    $res = M("data_xq")->add($data);
                    if ($res) {
                        $this->success('需求提交成功', 'index');
                    } else {
                        $this->error('数据异常,请稍后再试！');
                    }
                } else {
                    $this->error('参数有误,请填写完整！');
                }
            } else {
                $this->error('参数有误,请填写完整！');
            }
        } else {
            $this->display();
        }
    }

    public function double()
    {
        $map['status'] = 1;
        $map["type"] = "居民区";
        $data = M('dzz')->where($map)->select();
        $this->assign('data', $data);
        $this->display();
    }

    public function double2($id=0)
    {
        if (IS_POST) {
            $data["name"] = $_POST["name"];
            $data["tel"] = $_POST["mobile"];
            $data["sex"] = $_POST["sex"];
            $data["gzdw"] = $_POST["gzdw"];
            $data["ydzz"] = $_POST["ydzz"];
            $data["addr"] = $_POST["addr"];
            $data["dzz"] = $_POST["sbddzz"];
            $data["sbdstate"] = 0;
            if (!($data["name"] && $data["tel"] && $data["dzz"])) {
                $this->error('参数有误,请填写完整！');
            }

            $map['name'] = $data["name"];
            $map['status'] = 1;
            $map['tel'] = $data["tel"];
            $map['dzz'] = $data["dzz"];
            $map['sbd'] = 1;
            $partymember = M("party_member")->where($map)->find();
//            $sql = M()->getlastsql();
            if ($partymember) {
                $data["id"] = $partymember["id"];
                $sbdpmid = M("party_member")->save($data);
            } else {
                $data["sbd"] = 1;
                $sbdpmid = M("party_member")->add($data);
            }
            if (!$sbdpmid) {
                $this->error("报道失败,请稍后再试");
            }else{
                $this->success('报到成功',"index");
            }

//            $data2["sbdpmid"] = $sbdpmid;
//            $map2['openid'] = $wxmember['openid'];
//            $save = M("wxmember")->where($map2)->save($data2);
//            if ($save) {
//                $this->success('报到成功');
//            } else {
//                $this->error("报道失败,请稍后再试" . $sbdpmid);
//            }
        } else {
            $dzz = M('dzz')->where('id=' . $id)->find();
            $this->assign('dzz', $dzz);
            $this->display();
        }
    }

    public function project()
    {
        $map["status"] = 1;
        $data = M("project_claim")->where($map)->select();
        foreach ($data as &$v) {
            if ($v["pic1"]) {
                $pics = explode(",", $v["pic1"]);
                $v["picpath"] = get_cover($pics[0], "path");
                $v["picpath2"] = get_cover($pics[1], "path");
            }
        }
        unset($v);
        $this->assign("list",$data);
        $this->display();
    }
    public function projectDetail($id)
    {
        if(IS_POST){
            $data["aid"] = $id;
            $data["pj"] =$_POST["pj"];
            $data["content"] =$_POST["content"];
            $data["createtime"] =time();
            if ($data['aid'] && $data['pj'] && $data['content']) {
                $res = M("data_pj")->add($data);
                if ($res > 0) {
                    $this->success('评价成功，谢谢您的参与！');
                } else {
                    $this->error('数据异常,请稍后再试！');
                }
            } else {
                $this->error('参数有误,请填写完整！');
            }
        }else {
            $map["id"] = $id;
            $data = M("project_claim")->where($map)->find();
            if ($data["pic1"]) {
                $pics = explode(",", $data["pic1"]);
                $this->assign("pics", $pics);
            }
            $this->assign("data", $data);
            $this->assign('toptitle', '项目详情');
            $map2["status"] = 1;
            $map2["aid"] = $id;
            $map2["state"] = 1;
            $rldata = M("data_gycrl")->where($map2)->select();
            foreach ($rldata as &$v) {
                $map3["id"] = array("in", $v["rltype"]);
                $types = M("data_rltype")->where($map3)->select();
                $v["rltype_str"] = implode(",", array_column($types, "name"));
                if ($v['qt']) {
                    if ($v["rltype_str"]) {
                        $v["rltype_str"] .= "," . $v['qt'];
                    } else {
                        $v["rltype_str"] = $v['qt'];
                    }
                }
            }
            unset($v);

            $pjcount['pj1']=M("data_pj")->where(array("status"=>1,"state"=>1,"aid"=>$id,"pj"=>1))->count();
            $pjcount['pj2']=M("data_pj")->where(array("status"=>1,"state"=>1,"aid"=>$id,"pj"=>2))->count();
            $pjcount['pj3']=M("data_pj")->where(array("status"=>1,"state"=>1,"aid"=>$id,"pj"=>3))->count();
            $pjcount['pj4']=M("data_pj")->where(array("status"=>1,"state"=>1,"aid"=>$id,"pj"=>4))->count();

            $pjdata = M("data_pj")->where(array("status"=>1,"state"=>1,"aid"=>$id))->select();
            $this->assign('pjcount', $pjcount);
            $this->assign('pjdata', $pjdata);

            $this->assign('rldata', $rldata);
            $this->display("projectDetail");
        }
    }
    public function projectClaim($id)
    {
        $data = M("project_claim")->find($id);
        $map2["status"] = 1;
        $map2["aid"] = $id;
        $rldata = M("data_gycrl")->where($map2)->select();
        $this->assign('rldata', $rldata);
        $this->assign("data", $data);
        $rltypelist = M("data_rltype")->where(array("status" => 1,"aid"=>$id))->select();
        $this->assign('rltypelist', $rltypelist);
        if (in_array($id, array(15, 14, 28))) {
            $this->display("projectClaim");
        } elseif (in_array($id, array(13, 17, 18,20))) {
            $this->display("projectClaim2");
        }
    }
//    public function projectClaim2()
//    {
//        $this->display("projectClaim2");
//    }

    public function gycrl($aid)
    {
        if (IS_POST) {
            $data = $_POST;
            $data["create_time"] = time();
            $project = M("project_claim")->find($data["aid"]);
            if ($project["id"] == 14) {
                //公益进工地
                if (!is_phone_number($data["mobile"])) {
                    $this->error('请输入正确的手机号！');
                }
                if ($data["rllx"] == 1) {//个人
                    $data["name"] = $data["name2"];
                    if ($data["name"] && $data["mobile"] && $data["rltype"] && $data["aid"]) {
                        $res = M("data_gycrl")->add($data);
                        if ($res) {
                            $this->success('认领成功', "index");
                        } else {
                            $this->error('数据异常,请稍后再试！');
                        }
                    } else {
                        $this->error('参数有误,请填写完整！');
                    }
                } elseif ($data["rllx"] == 2) {//单位
                    $data["name"] = $data["name1"];
                    if ($data["dw"] && $data["name"] && $data["mobile"] && $data["rltype"] && $data["aid"]) {
                        $res = M("data_gycrl")->add($data);
                        if ($res) {
                            $this->success('认领成功', "index");
                        } else {
                            $this->error('数据异常,请稍后再试！');
                        }
                    } else {
                        $this->error('参数有误,请填写完整111！');
                    }
                } else {
                    $this->error('参数有误,请填写完整2222！');
                }
            } elseif ($project["id"] == 15) {
                //爱心助学
                if (!is_phone_number($data["mobile"])) {
                    $this->error('请输入正确的手机号！');
                }
                if ($data["rllx"] == 1) {//个人
                    $data["sum"] = intval($data["sum"]);
                    $data["name"] = $data["name2"];
                    if ($data["name"] && $data["mobile"] && $data["sum"] && $data["aid"]) {
                        $res = M("data_gycrl")->add($data);
                        if ($res) {
                            $this->success('认领成功', "index");
                        } else {
                            $this->error('数据异常,请稍后再试！');
                        }
                    } else {
                        $this->error('参数有误,请填写完整！');
                    }
                } elseif ($data["rllx"] == 2) {//单位
                    $data["sum"] = intval($data["sum"]);
                    $data["name"] = $data["name1"];
                    if ($data["dw"] && $data["name"] && $data["mobile"] && $data["sum"] && $data["aid"]) {
                        $res = M("data_gycrl")->add($data);
                        if ($res) {
                            $this->success('认领成功', "index");
                        } else {
                            $this->error('数据异常,请稍后再试！');
                        }
                    } else {
                        $this->error('参数有误,请填写完整！');
                    }
                } else {
                    $this->error('参数有误,请填写完整！');
                }

            } elseif ($project["id"] == 18) {
                if (!is_phone_number($data["mobile"])) {
                    $this->error('请输入正确的手机号！');
                }
                //青丝行动
                if ($data["name"] && $data["sex"] && $data["mobile"] && $data["aid"]) {
                    $res = M("data_gycrl")->add($data);
                    if ($res) {
                        $this->success('认领成功', "index");
                    } else {
                        $this->error('数据异常,请稍后再试！');
                    }
                } else {
                    $this->error('参数有误,请填写完整！');
                }
            }elseif ($project["id"] == 13) {
                if (!is_phone_number($data["mobile"])) {
                    $this->error('请输入正确的手机号！');
                }
                //户外公益赛
                if ($data["name"] && $data["sex"] && $data["mobile"] && $data["aid"] && $data["idnum"]) {
                    $res = M("data_gycrl")->add($data);
                    if ($res) {
                        $this->success('认领成功', "gyc");
                    } else {
                        $this->error('数据异常,请稍后再试！');
                    }
                } else {
                    $this->error('参数有误,请填写完整！');
                }
            }elseif ($project["id"] == 17) {
                if (!is_phone_number($data["mobile"])) {
                    $this->error('请输入正确的手机号！');
                }
                //青丝行动
                if ($data["name"] && $data["dw"] && $data["mobile"] && $data["aid"]) {
                    $res = M("data_gycrl")->add($data);
                    if ($res) {
                        $this->success('认领成功', "index");
                    } else {
                        $this->error('数据异常,请稍后再试！');
                    }
                } else {
                    $this->error('参数有误,请填写完整！');
                }
            } elseif ($project["id"] == 20) {
                if (!is_phone_number($data["mobile"])) {
                    $this->error('请输入正确的手机号！');
                }
                //独居老人服务
                if ($data["name"] &&( $data["rltype"] || $data['qt']) && $data["mobile"] && $data["aid"]) {
                    $res = M("data_gycrl")->add($data);
                    if ($res) {
                        $this->success('认领成功', "index");
                    } else {
                        $this->error('数据异常,请稍后再试！');
                    }
                } else {
                    $this->error('参数有误,请填写完整！');
                }
            }  elseif ($project["id"] == 28) {
                //余香ROSE
                if (!is_phone_number($data["mobile"])) {
                    $this->error('请输入正确的手机号！');
                }
                if ($data["rllx"] == 1) {//个人
                    $data["name"] = $data["name2"];
                    if ($data["name"] && $data["mobile"]&& $data["aid"]) {
                        $res = M("data_gycrl")->add($data);
                        if ($res) {
                            $this->success('认领成功', "index");
                        } else {
                            $this->error('数据异常,请稍后再试！');
                        }
                    } else {
                        $this->error('参数有误,请填写完整！');
                    }
                } elseif ($data["rllx"] == 2) {//单位
                    $data["name"] = $data["name1"];
                    if ($data["dw"] && $data["name"] && $data["mobile"] && $data["aid"]) {
                        $res = M("data_gycrl")->add($data);
                        if ($res) {
                            $this->success('认领成功', "index");
                        } else {
                            $this->error('数据异常,请稍后再试！');
                        }
                    } else {
                        $this->error('参数有误,请填写完整！');
                    }
                } else {
                    $this->error('参数有误,请填写完整！');
                }

            } else {
                $this->error('待完善');
            }
        }
    }

    public function source()
    {
        $type = I("type", 3, "intval");
        $this->assign('type', $type);
        $this->display();
    }
    public function zylist1($type)
    {
        $map['type'] = $type;
        $list = M('data_zyqd')->where($map)->select();
        $res['result'] = 1;
        $res['list'] = $list;
        header("Content-Type:text/html; charset=utf-8");
        foreach ($list as &$v) {
            if (!$v['lat'] && $v['addr']) {
                $geores = geoEncode($v['addr']);
                if ($geores) {
                    $v['lat'] = $geores['lat'];
                    $v['lng'] = $geores['lng'];
                    $geores['id'] = $v['id'];
                    M("data_zyqd")->save($geores);
                }
            }
        }
        echo json_encode($res);
        exit;
    }
    public function zylist2($type, $page = 1, $rows = 10)
    {
        $map['type'] = $type;
        $list = M('data_zyqd')->where($map)->page($page, $rows)->select();
        if (!$list) {
            $list = array();
        }
        $res['result'] = 1;
        $res['data'] = $list;
        header("Content-Type:text/html; charset=utf-8");
        foreach ($list as &$v) {
            if (!$v['lat'] && $v['addr']) {
                $geores = geoEncode($v['addr']);
                if ($geores) {
                    $v['lat'] = $geores['lat'];
                    $v['lng'] = $geores['lng'];
                    $geores['id'] = $v['id'];
                    M("data_zyqd")->save($geores);
                }
            }
        }
        echo json_encode($res);
        exit;
    }
    

    public function cySend()
    {
        $this->display('cySend');
    }
    public function cyReceive()
    {
        $this->display('cyReceive');
    }
    public function cyDetail()
    {
        $this->display('cyDetail');
    }
    public function yshQyfcList()
    {
        $this->display('yshQyfcList');
    }
    public function yshDetail()
    {
        $this->display('yshDetail');
    }
    public function conferenceList()
    {
        $this->display('conferenceList');
    }
    public function conferenceNew()
    {
        $this->display('conferenceNew');
    }
    public function teleList()
    {
        $this->display('teleList');
    }
}
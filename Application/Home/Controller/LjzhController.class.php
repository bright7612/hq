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
 * 合庆横屏
 */
class LjzhController extends Controller
{
    public function index()
    {
        $this->display();
    }

    public function activityIndex()
    {
        $this->display("activityIndex");
    }

    public function schoolIndex()
    {
        $map["status"] = 1;
        $map['datetime'] = array("gt", time());
        $list = M("apply_djkc")->where($map)->order("datetime asc")->limit(3)->select();
        $this->assign("list",$list);

        $this->display("schoolIndex");
    }
    public function schoolMore()
    {
        $map["status"] = 1;
//        $map['datetime'] = array("gt", time());
        $list = M("apply_djkc")->where($map)->order("datetime desc")->select();
        foreach ($list  as &$v){
            $v["canbm"] = $v["datetime"]>time()?1:0;
        }
        unset($v);

        $this->assign("data",$list);
        $this->display("schoolMore");
    }
    public function schoolBm()
    {
        $map["status"] = 1;
        $map["id"] = I("id",0,"intval");
        $info = M("apply_djkc")->where($map)->find();
        if($info) {
            $info['qrcode'] = '/home/index/qrcode/type/dkbm/id/' . $info['id'];
        }
        $this->assign("data",$info);
        $this->display("schoolBm");
    }


    public function serverIndex()
    {
        $this->display("serverIndex");
    }

    public function serverDetail()
    {
        $this->display("serverDetail");
    }



    public function activityList()
    {
//        $map["status"] = 1;
//        $list=M("data_djhd")->where($map)->select();
//        $this->assign("data",$list);
//        $this->display("activityList");

        $map["status"] = 1;
        $map["type"] = 1;
//        $map["date"] = array("gt",time());
        $list=M("data_hdyg")->where($map)->order("date desc")->select();
        $this->assign("data",$list);
        $this->display("activityList");
    }
    public function activityDetail()
    {
        $map["status"] = 1;
        $map["id"] = I("id",0,"intval");
        $info = M("data_hdyg")->where($map)->find();
        if($info) {
            $info['qrcode'] = '/home/index/qrcode/type/hdxq/id/' . $info['id'];
        }
        $info["canbm"] = $info["date"]>time()?1:0;
        $this->assign("data",$info);
        $this->display("activityDetail");
    }

    public function openIndex()
    {
        $this->display("openIndex");
    }

    public function projectList()
    {
        $map["status"] = 1;
        $list = M("project_claim")->where($map)->select();
        $this->assign("data",$list);
        $this->display("projectList");
    }

    /**
     *
     */
    public function projectDetail()
    {
        $map["status"] = 1;
        $map["id"] = I("id",0,"intval");
        $info = M("project_claim")->where($map)->find();
        $this->assign("data",$info);
        if($info['pic1']){
            $this->assign("pics",explode(",",$info["pic1"]));
        }
        $this->display("projectDetail");
    }

    public function resourceList()
    {
        $this->display("resourceList");
    }

    public function resourceList2($type =1)
    {
        $titleopt = array(array("id" => 1, "value" => "居民区与驻区单位联建清单"),
            array("id" => 2, "value" => "合庆党建服务站点清单"),
            array("id" => 3, "value" => "合庆街道活动场地清单"),
            array("id" => 4, "value" => "“合庆公益城”驻区单位资源清单"),
            array("id" => 5, "value" => "点亮心愿 社区单位认领心愿"),
            array("id" => 6, "value" => "公益进工地认领清单"),
            array("id" => 7, "value" => "白领课堂"),
            array("id" => 8, "value" => "社会组织服务清单"),
            array("id" => 9, "value" => "合庆街道自治金项目清单"),
            array("id" => 10, "value" => "社区文化活动清单"),
            array("id" => 11, "value" => "金融服务进社区清单"),
            array("id" => 12, "value" => "群团服务项目清单"),
            array("id" => 13, "value" => "社区事务受理事项清单"),
            array("id" => 14, "value" => "拥军优属服务项目清单")
        );
        $titleopt = array_combine(array_column($titleopt,"id"),array_column($titleopt,"value"));
        $this->assign("title".$titleopt[$type]);
        $map['status'] = 1;
        $map["type"]  =$type;
        $list = M("data_zyqd")->where($map)->select();
        $this->assign("data",$list);
        $this->assign("type",$type);
        $this->display("resourceList2");
    }




    public  function serveStationList (){
        $map["status"] = 1;
        $list = M('data_fwzd')->where($map)->select();
        $this->assign("data",$list);
        $this->display("serveStationList");
    }


    public function schoolDetail()
    {
        $this->display("schoolDetail");
    }

    /**
     * 组织架构
     */
    public function openStructure()
    {
        $this->display("openStructure");
    }

    /**
     * 群团
     * '群团活动类型 1工会2妇联3共青团
     */
    public function qt()
    {
        $map["status"] = 1;
        $map['qttype'] = 1;
        $ghhd =  M("data_hdyg")->where($map)->find();
        $ghhd["pic"] = explode(",",$ghhd['pic1'])[0];
        $map['qttype'] = 2;
        $flhd =  M("data_hdyg")->where($map)->find();
        $flhd["pic"] = explode(",",$flhd['pic1'])[0];
        $map['qttype'] = 3;
        $gqthd =  M("data_hdyg")->where($map)->find();
        $gqthd["pic"] = explode(",",$gqthd['pic1'])[0];

        $this->assign("ghhd",$ghhd);
        $this->assign("flhd",$flhd);
        $this->assign("gqthd",$gqthd);
        $this->display("qt");
    }

    /**
     * 群团列表
     */
    public function qtList(){
        $map["status"] = 1;
        $map["qttype"]= I("qttype",1,"intval");
        $qtopt =array("1"=>"工会群团活动","2"=>"妇联群团活动","3"=>"共青团群团活动");
        $this->assign("title",$qtopt[$map["qttype"]]);
        $list = M("data_hdyg")->where($map)->select();
        $this->assign("data",$list);
        $this->display("qtList");
    }

    /**
     * 群团详情
     */
    public function qtDetail(){
        $map["status"] = 1;
        $map["id"] = I("id",0,"intval");
        $info = M("data_hdyg")->where($map)->find();
        $this->assign("data",$info);

        $qtopt =array("1"=>"工会群团活动","2"=>"妇联群团活动","3"=>"共青团群团活动");
        $this->assign("title",$qtopt[$info["qttype"]]);

        if($info['pic1']){
            $this->assign("pics",explode(",",$info["pic1"]));
        }
        $this->display("qtDetail");
    }

    public function requireList(){
        $this->display("requireList");
    }

    public function serverLouyu(){
        $this->display("serverLouyu");
    }

    public function zhiduList(){
        $map["status"] =1;
        $list = M("issue_content")->where($map)->select();


        $this->assign("data",$list);
        $this->display("zhiduList");
    }

    /**
     *
     */
    public function zhiduDetail()
    {
        $map["status"] = 1;
        $map["id"] = I("id",0,"intval");
        $info = M("issue_content")->where($map)->find();
        $this->assign("data",$info);
//        if($info['pic1']){
//            $this->assign("pics",explode(",",$info["pic1"]));
//        }
        $this->display("zhiduDetail");
    }


    public function zuzhiList(){
        $list = M("dzz")->where(array("status"=>1))->order("type")->select();
        $this->assign("data",$list);
        $this->display("zuzhiList");
    }


    public function zuzhiDetail(){
        $map["status"] = 1;
        $map["id"] = I("id",0,"intval");
        $info = M("dzz")->where($map)->find();
        $this->assign("data",$info);
//        if($info['pic1']){
//            $this->assign("pics",explode(",",$info["pic1"]));
//        }
        $this->display("zuzhiDetail");
    }

    public function cdmap()
    {
        $typeopt = array(1 => "居民区与驻区单位联建清单",
            2 => "合庆党建服务站点清单",
            3 => "合庆街道活动场地清单",
            4 => "“合庆公益城”驻区单位资源清单",
            5 => "点亮心愿 社区单位认领心愿",
            6 => "公益进工地认领清单",
            7 => "白领课堂",
            8 => "社会组织服务清单",
            9 => "合庆街道自治金项目清单",
            10 => "社区文化活动清单",
            11 => "金融服务进社区清单",
            12 => "群团服务项目清单",
            13 => "社区事务受理事项清单",
            14 => "拥军优属服务项目清单"
        );
        $type = I("type", 1, "intval");
        $this->assign('type', $type);
        if ($type == 2 || $type == 3 || $type == 7 || $type == 8 || $type == 10 || $type == 14) {
            $this->assign('toptitle', '资源清单');
            $this->display();
        }
//        else {
//            $this->assign('title', $typeopt[$type]);
//            $this->display("zylist");
//        }
    }

    public function actmap()
    {
        $typeopt = array(1 => "居民区与驻区单位联建清单",
            2 => "合庆党建服务站点清单",
            3 => "合庆街道活动场地清单",
            4 => "“合庆公益城”驻区单位资源清单",
            5 => "点亮心愿 社区单位认领心愿",
            6 => "公益进工地认领清单",
            7 => "白领课堂",
            8 => "社会组织服务清单",
            9 => "合庆街道自治金项目清单",
            10 => "社区文化活动清单",
            11 => "金融服务进社区清单",
            12 => "群团服务项目清单",
            13 => "社区事务受理事项清单",
            14 => "拥军优属服务项目清单"
        );
        $type = I("type", 1, "intval");
        $this->assign('type', $type);
        if ($type == 2 || $type == 3 || $type == 7 || $type == 8 || $type == 10 || $type == 14) {
            $this->assign('toptitle', '资源清单');
            $this->display();
        }
//        else {
//            $this->assign('title', $typeopt[$type]);
//            $this->display("zylist");
//        }
    }


    public function zylist1($type)
    {
        $map['type'] = $type;
        $map['status'] = 1;
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
        $map['status'] = 1;
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

    public function zyqdqrc($type=1)
    {
        $list = array(1 => "居民区与驻区单位联建清单",
            2 => "合庆党建服务站点清单",
            3 => "合庆街道活动场地清单",
            4 => "“合庆公益城”驻区单位资源清单",
            5 => "点亮心愿 社区单位认领心愿",
            6 => "公益进工地认领清单",
            7 => "白领课堂",
            8 => "社会组织服务清单",
            9 => "合庆街道自治金项目清单",
            10 => "社区文化活动清单",
            11 => "金融服务进社区清单",
            12 => "群团服务项目清单",
            13 => "社区事务受理事项清单",
            14 => "拥军优属服务项目清单"
        );
        header("Content-Type:text/html; charset=utf-8");
        foreach ($typeopt as $k=>$v) {

        }
        $this->assign('toptitle', '资源清单');
        $this->assign('list', $list);
        $this->display();


    }


}
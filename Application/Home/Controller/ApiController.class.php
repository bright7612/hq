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

class ApiController extends Controller
{
    public function dzb(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
        $map["status"] = 1;

        $list = M("dzz") ->where($map)->field("id,name")->select();

        $res["status"] = 1;
        $res["result"]["dzbNum"] = count($list);
        $map2["status"] = 1;
        $map2["dymc"] = 1;
        $res["result"]["people"] = M("party_member")->where($map2)->count();

        $showData = array();
        foreach ($list  as $k=>$v){
            if($k<5) {
                $showData[] = $v;
            }
        }
        array_push($showData,array("id"=>0,"name"=>"更多..."));
        $res["result"]["showData"] = $showData;
        $res["result"]["allData"] = $list;

        echo json_encode($res);exit;
    }

    public function dy($dzbId){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
        $map["status"] = 1;
        $map["dzz"] = $dzbId;
        $map["dymc"] = 1;
        $list = M("party_member") ->where($map)->field("id,name")->select();

        $res["status"] = 1;
        $res["result"]["people"] = count($list);

        $showData = array();
        foreach ($list  as $k=>$v){
            if($k<5) {
                $showData[] = $v;
            }
        }
        array_push($showData,array("id"=>0,"name"=>"更多..."));
        $res["result"]["showData"] = $showData;
        $res["result"]["allData"] = $list;

        echo json_encode($res);exit;
    }

    public function peopleRank(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
        $map["status"] = 1;
        $map["dymc"] = 1;
        $list = M("party_member") ->where($map)->field("id,name")->limit(100)->select();

        $res["status"] = 1;
        $res["result"]["increase"] = 3231;

        $showData = array();
        foreach ($list  as $k=>&$v){
            if($k==0){
                $v["score"] = 100;
                $v["condition"] = 1;
            }else {
                $v["score"] = $list[$k-1]["score"] - rand(0, 2);
                $v["score"] = max($v["score"],60);
                $v["condition"] = rand(0,1)?1:-1;
            }
            $v["rank"] = $k+1;
            if($k<10) {
                $showData[] = $v;
            }
        }
        unset($v);
        $res["result"]["showData"] = $showData;
        $res["result"]["allData"] = $list;

        echo json_encode($res);exit;
    }

    public function orgRank(){
        header("Access-Control-Allow-Origin:*");
        header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
        $map["status"] = 1;
        $list = M("dzz") ->where($map)->field("id,name")->select();

        $res["status"] = 1;
        $res["result"]["increase"] = 7896;

        $showData = array();
        foreach ($list  as $k=>&$v){
            if($k==0){
                $v["score"] = 100;
                $v["condition"] = 1;
            }else {
                $v["score"] = $list[$k-1]["score"] - rand(0, 2);
                $v["score"] = max($v["score"],60);
                $v["condition"] = rand(0,1)?1:-1;
            }
            $v["rank"] = $k+1;
            if($k<10) {
                $showData[] = $v;
            }
        }
        unset($v);
        $res["result"]["showData"] = $showData;
        $res["result"]["allData"] = $list;

        echo json_encode($res);exit;
    }


    public function getApplied($id){
        $map['aid'] = $id;
        $map['status'] = 1;
        $map['state'] = 1;
        $map["starttime"] = array("gt", time());
        $list = M("data_hdbm")->where($map)->order("starttime asc")->select();
        if ($list) {
            foreach ($list as &$v) {
                //$v["yytime"] = date("Y-m-d H:i:s", $v["starttime"]) . " 至 " . date("Y-m-d H:i:s", $v["endtime"]);
                $v["startstr"] = date("Y-m-d H:i:s", $v["starttime"]);
                $v["endstr"] = date("Y-m-d H:i:s", $v["endtime"]);
            }
            unset($v);
        }
        $res["status"] = 1;
        $res["data"] = $list;

        echo json_encode($res);
    }


}

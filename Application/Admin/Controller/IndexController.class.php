<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use Admin\Builder\AdminListBuilder;
use Think\Page;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class IndexController extends AdminController
{

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index()
    {

        if (UID) {

            if (IS_POST) {
                $count_day = I('post.count_day', C('COUNT_DAY'), 'intval', 7);
                if (M('Config')->where(array('name' => 'COUNT_DAY'))->setField('value', $count_day) === false) {
                    $this->error(L('_ERROR_SETTING_') . L('_PERIOD_'));
                } else {
                    S('DB_CONFIG_DATA', null);
                    $this->success(L('_SUCCESS_SETTING_') . L('_PERIOD_'), 'refresh');
                }

            } else {
//                $this->meta_title = L('_INDEX_MANAGE_');
//                $today = date('Y-m-d', time());
//                $today = strtotime($today);
//                $count_day = C('COUNT_DAY',null,7);
//                $count['count_day']=$count_day;
//                for ($i = $count_day; $i--; $i >= 0) {
//                    $day = $today - $i * 86400;
//                    $day_after = $today - ($i - 1) * 86400;
//                    $week_map=array('Mon'=>L('_MON_'),'Tue'=>L('_TUES_'),'Wed'=>L('_WEDNES_'),'Thu'=>L('_THURS_'),'Fri'=>L('_FRI_'),'Sat'=>'<strong>'.L('_SATUR_').'</strong>','Sun'=>'<strong>'.L('_SUN_').'</strong>');
//                    $week[] = date('m月d日 ', $day). $week_map[date('D',$day)];
//                    $user = UCenterMember()->where('status=1 and reg_time >=' . $day . ' and reg_time < ' . $day_after)->count() * 1;
//                    $registeredMemeberCount[] = $user;
//                    if ($i == 0) {
//                        $count['today_user'] = $user;
//                    }
//                }
//                $week = json_encode($week);
//                $this->assign('week', $week);
//                $count['total_user'] = $userCount = UCenterMember()->where(array('status' => 1))->count();
//                $count['today_action_log'] = M('ActionLog')->where('status=1 and create_time>=' . $today)->count();
//                $count['last_day']['days'] = $week;
//                $count['last_day']['data'] = json_encode($registeredMemeberCount);
                // dump($count);exit;
                $admindzz = session("dzzid");
                if($admindzz){
//                    $map1["status"] = 1;
//                    $map1['state'] = 0;
//                    $map1['aid'] = array("in",M("data_hdyg")->where(array("status"=>1,"dzz"=>$admindzz))->select());
//                    $count['hd'] = M('data_hdbm')->where($map1)->count();
                    $reshdbm = M()->query("select  count(*) from ljz_data_hdbm a JOIN ljz_data_hdyg b on a.aid = b.id WHERE a.status = 1 and a.state = 0  AND b.dzz =  $admindzz AND b.type =  6");
                    $count['hd'] = $reshdbm[0]["count(*)"];
                    $count['wxbd'] = M('wxmember')->where(array("status" => 1, "pmid is null or pmid = 0 ", "dzz" => $admindzz))->count();

                    $this->assign('count', $count);
                    $this->display("ljzzbindex");
                }else {

                    $map1["status"] = 1;
                    $map1['state'] = 0;
                    $count['zzj'] = M('data_xmbm')->where($map1)->count();
                    $count['dk'] = M('data_dkbm')->where($map1)->count();
                   // $count['hd'] = M('data_hdbm')->where($map1)->count();
                    //场地预约
                    $rescdyy =M()->query("select  count(*) from ljz_data_hdbm a JOIN ljz_data_hdyg b on a.aid = b.id WHERE a.status = 1 and a.state = 0  AND b.type =  6");
                    $count['hd'] = $rescdyy[0]["count(*)"];
                    $count['dzz'] = M('dzz')->where(array("status" => 1))->count();
                    $count['xmrl'] = M('data_gycrl')->where(array("status" => 1,"state"=>0))->count();
                    $count['dy'] = M('party_member')->where(array("status" => 1))->count();
                    $count['wxbd'] = M('wxmember')->where(array("status" => 1, "pmid is null or pmid = 0 ", "dzz" => array("neq", 0)))->count();

                    $dzzcount = M("dzz")->group("type")->where(array("status"=>1))->field("type,count(*)  as count")->select();
                    $dzzCountData = array();
                    foreach ($dzzcount as $v) {
                        $dzzCountData[] = array($v["type"], intval($v["count"]));
                    }
                    $this->assign('dzzCountData', json_encode($dzzCountData));

//                //组装highchart  drilldown需要的数据
//                $dzzChartData = array();
//                $dzzDrilldownData =array();
//                $dzzdata = M("dzz")->where(array("status" => 1))->group("type")->field("type")->select();
//                foreach ($dzzdata   as $v){
//                    $ids = M("dzz")->where(array("status" => 1,"type"=>$v["type"]))->field("id,name")->select();
//                    $mapcy["dzz"] =array("in",array_column($ids,"id"));
//                    $mapcy["status"] = 1;
//                    $item["name"]=$v["type"];
//                    $item["y"]=intval(M("party_member")->where($mapcy)->count());
//                    $item["drilldown"] = $v["type"];
//                    $dzzChartData[]  =$item;
//
//                    $drilldownItem = array("id"=>$v["type"],"name"=>$v["type"]);
//                    $cy1 = M("party_member")->where($mapcy)->field("dzz,count(*) as count")->group("dzz")->select();
//                    $cyarr = array_combine(array_column($cy1,"dzz"),array_column($cy1,"count"));
//                    foreach ($ids as $v1){
//                        $drilldownItem["data"][] = array($v1["name"],intval($cyarr[$v1["id"]]));
//                    }
//                    $dzzDrilldownData[] = $drilldownItem;
//                }
//
//                $this->assign('dzzChartData', json_encode($dzzChartData));
//                $this->assign('dzzDrilldownData', json_encode($dzzDrilldownData));
                    $this->assign('count', $count);
                    $this->display("ljzindex");
                }
            }


        } else {
            $this->redirect('Public/login');
        }
    }

    public function dzz($page = 1, $r = 10)
    {

        $type =   I("dzztype","全部");
        if($type != "全部"){
           $map["type"] = $type;
        }
        $map["status"] = 1;
        $model = M('dzz');
        $list = $model->where($map)->order("num desc")->page($page, $r)->select();
        $i =1;
        foreach ($list as &$v){
            $v['membercount'] = M("party_member")->where(array("dzz"=>$v['id']))->count();
            $v["index"]=($page-1)*10+$i++;
        }

        $totalCount = $model->where($map)->count();

        $dzztypeoptions = M("dzz")->group("type")->where(array("status"=>1))->field("type")->select();
        array_unshift($dzztypeoptions,array("type"=>"全部"));

        $this->assign("dzztype_options",$dzztypeoptions);

        $this->assign("list",$list);
        $pager = new \Think\Page($totalCount, $r, $_REQUEST);
        $pager->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $paginationHtml = $pager->show();
        $this->assign('pagination', $paginationHtml);


        $this->display();

    }

    public function dzzcy($page = 1, $r = 10)
    {
        $dzz = I("dzz",0,"intval");
        $map["status"] = 1;
        if($dzz){
            $map["dzz"] = $dzz;
            $dzzitem = M("dzz")->find($dzz);
            $this->assign("dzzname",$dzzitem['name']);

        }
        $model = M('party_member');
        $list = $model->where($map)->page($page, $r)->select();
        $i =1;
        $sexopt = array("1"=>"男","2"=>"女");
        foreach ($list as &$v){
            $v["index"]=($page-1)*10+$i++;
            $dzzitem=M("dzz")->find($v['dzz']);
            $v['dzzname'] = $dzzitem['name'];
            $v['sex'] = $sexopt[$v['sex']];
        }
        unset($v);
        $totalCount = $model->where($map)->count();

        $dzzlist  = M('dzz')->where(array('status'=>1))->order('type,id')->select();
        $dzzoptions = array(
            array('id' => 0, 'value' => "全部","lvl"=> 2),
        );

        $curtype = array();
        foreach ($dzzlist as $v1){
            if($v1['type']!=$curtype['type']){
                $curtype = $v1;
                $dzzoptions[] =array('id'=>"",'value'=>$v1['type'],"lvl"=>1);
            }
            $dzzoptions[] =array('id'=>$v1['id'],'value'=>$v1['name'],"lvl"=> 2);
        }
        $this->assign("dzz_options",$dzzoptions);

        $this->assign("dzz",$dzz);

        $this->assign("list",$list);
        $pager = new \Think\Page($totalCount, $r, $_REQUEST);
        $pager->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $paginationHtml = $pager->show();
        $this->assign('pagination', $paginationHtml);
        $this->display();

    }

}

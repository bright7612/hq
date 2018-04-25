<?php
/**
 * Created by PhpStorm.
 * User: caipeichao
 * Date: 14-3-11
 * Time: PM5:41
 */

namespace Admin\Controller;

use Admin\Builder\AdminConfigBuilder;
use Admin\Builder\AdminListBuilder;
use Admin\Builder\AdminTreeListBuilder;


class LjzController extends AdminController
{
    protected $issueModel;

//    function _initialize()
//    {
//
//    }

    public function maptest()
    {
        //$this->display();

        $builder = new AdminConfigBuilder();
        $builder->title("测试")->keyText('title', "Title")->keyBDMap("address|lng|lat", "地址", "")
            ->keyStatus()->keyCreateTime()->keyUpdateTime()->data(array("address" => "上海市XXXXX", "lat" => "31.243001", "lng" => "121.532246"))
            ->buttonBack()->display();
    }


    public function index($page = 1, $r = 10)
    {

        $this->redirect("dyxs");

        //读取列表
        $map = array('status' => 1);
        $model = M('trans_org_relation');
        $list = $model->where($map)->page($page, $r)->select();
        foreach ($list as $k => $v) {
            if ($v['operation'] == 1) $list[$k]['operation'] = '组织关系转入';
            if ($v['operation'] == 2) $list[$k]['operation'] = '组织关系转出';
            if ($v['work_condition'] == 2) $list[$k]['work_condition'] = '在职党员';
            if ($v['work_condition'] == 2) $list[$k]['work_condition'] = '非在职党员';

        }
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';


        $builder->title('组织关系转接')
            ->setStatusUrl(U('setTransorgStatus'))->buttonDelete()
            ->keyId()->keyText('name', '姓名')->keyText('mobile', '电话')->keyText('addr', '地址')->keyText('work_condition', '在职')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setTransorgStatus()
    {
        $ids = I('ids');
        $status = I('get.status', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetStatus('trans_org_relation', $ids, $status);
    }

    /**
     * 长城家园
     */
    public function ccjy(){
        $this->display();
    }

    /**
     * 组织关系转接
     */
    public function zzgxzj(){
        $this->display();
    }

    /**
     * 党费试算
     */
    public function  dfss(){
        $this->display();
    }

    /**
     * 益浦东
     */
    public function  ypd(){
        $this->display();
    }
    /**
     * 区域单位
     */
    public function  qydw(){
        $this->display();
    }

    /**
     * 党员心声
     * @param int $page
     * @param int $r
     */
    public function dyxs($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 1);
        $model = M('voice');
        $list = $model->where($map)->page($page, $r)->select();
        $statemap = array("0" => "不展示", "1" => "展示");
        $anonymousopt = array("0" => "否", "1" => "是");
        foreach ($list as &$v) {
            $v["state_str"] = $statemap[$v['state']];
            if ($v['state'] == 0) {
                $v["_hide"] = array("隐藏");
            } else if ($v['state'] == 1) {
                $v["_hide"] = array("展示");
            }
            $v["anonymous_str"] = $anonymousopt[$v["anonymous"]];
        }
        unset($v);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';

        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $builder->title('意见反馈')
            ->setStatusUrl(U('setVoiceStatus'))
            ->buttonDelete()
            ->keyId()
            ->keyText("anonymous_str", "是否匿名")
            ->keyText('name', '姓名')
            ->keyText('org', '党组织')
            ->keyText('voice', '意见')
            ->keyText('feedback', '反馈')
            ->keyText("state_str", "前台是否展示")
            ->keyDoAction("setXsState?id=###&state=1", "展示", "操作", array('class' => 'ajax-get'))
            ->keyDoAction("setXsState?id=###&state=0", "隐藏", "操作", array('class' => 'ajax-get'))
            ->keyDoAction("xsFeedback?id=###", "反馈")
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function xsFeedback()
    {
        $id = I("id", 0, "intval");
        if (!$id) {
            $this->error("参数错误");
        }
        if (IS_POST) {
            $data = $_POST;
            $Model = M('voice');
            if ($Model->save($data) !== false) {
                $this->success(L('_SUCCESS_UPDATE_'), Cookie('__forward__'));
            } else {
                $this->error(L('_FAIL_UPDATE_'));
            }
        } else {
            $data = M("voice")->where(array("id" => $id))->find();
            $builder = new AdminConfigBuilder();
            $builder->title("意见反馈")
                ->keyLabel('voice', "意见建议")
                ->keyLabel('name', "建议人")
                ->keyLabel('org', "所属党组织")
                ->keyTextArea('feedback', "反馈")
                ->keyHidden("id", "")
                ->data($data)
                ->buttonSubmit()
                ->buttonBack()->display();
        }
    }


    public function setVoiceStatus()
    {
        $ids = I('ids');
        $status = I('get.status', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetStatus('voice', $ids, $status);

    }

    /**
     * 设置意见反馈是否展示
     */
    public function setXsState()
    {
        $ids = I('id');
        $state = I('get.state', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetState('voice', $ids, $state);
    }


    /**
     * 党费缴纳
     * @param int $page
     * @param int $r
     */
    public function dfjn($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 1);
        $model = M('dues_pay');
        $list = $model->where($map)->page($page, $r)->select();
        foreach ($list as $k => $v) {
            if ($v['operation'] == 1) $list[$k]['operation'] = '组织关系转入';
            if ($v['operation'] == 2) $list[$k]['operation'] = '组织关系转出';
            if ($v['work_condition'] == 2) $list[$k]['work_condition'] = '在职党员';
            if ($v['work_condition'] == 2) $list[$k]['work_condition'] = '非在职党员';

        }
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';


        $builder->title('党费缴纳')
            ->setStatusUrl(U('setDuespayStatus'))->buttonDelete()
            ->keyId()->keyText('name', '姓名')->keyText('mobile', '电话')->keyText('addr', '地址')->keyText('work_condition', '在职')
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setDuespayStatus()
    {
        $ids = I('ids');
        $status = I('get.status', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetStatus('dues_pay', $ids, $status);

    }

    /**
     * 公益城项目
     * @param int $page
     * @param int $r
     */
    public function gycxm($page = 1, $r = 10)
    {
        //读取列表
        $map["status"] = array("egt",0);
        $model = M('project_claim');
        $list = $model->where($map)->page($page, $r)->select();
        foreach ($list as $k => $v) {
            if ($v['operation'] == 1) $list[$k]['operation'] = '组织关系转入';
            if ($v['operation'] == 2) $list[$k]['operation'] = '组织关系转出';
            if ($v['work_condition'] == 2) $list[$k]['work_condition'] = '在职党员';
            if ($v['work_condition'] == 2) $list[$k]['work_condition'] = '非在职党员';
			
			$list[$k]['qrcode'] = '/home/index/qrcode/type/gycxq/id/'.$v['id'];

            if ($v['status'] == 1) {
                $list[$k]['_hide'] = array("启用");
                $list[$k]['status_str'] = "正常";
            } else if ($v['status'] == 0) {
                $list[$k]['_hide'] = array("禁用");
                $list[$k]['status_str'] = "禁用";
            }
        }


        unset($v);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';

        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $builder->title('项目直通车')
            ->buttonNew(U("editGycxm"))
            ->setStatusUrl(U('setProjectclaimStatus'))->buttonEnable()->buttonDisable()->buttonDelete()
            ->keyId()
            ->keyText('name', '名称')
            ->keyText('desc', '内容')
			->keyImage('qrcode', '二维码', array("width" => 24, "height" => 24 ,"style"=>"border-radius:0;"))
            ->keyText('status_str', '状态')
            ->keyDoActionEdit("editGycxm?id=###","编辑")
            ->keyDoAction("setProjectclaimStatus?ids=###&status=0", "禁用", "操作", array('class' => 'ajax-get'))
            ->keyDoAction("setProjectclaimStatus?ids=###&status=1", "启用", "操作", array('class' => 'ajax-get'))
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setProjectclaimStatus()
    {
        $ids = I('ids');
        $status = I('get.status', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetStatus('project_claim', $ids, $status);

    }

    /**
     * 编辑新增公益城项目
     */
    public function editGycxm()
    {
        $id = I("id", 0, "intval");
        $act = $id ? "编辑" : "新增";
        if (IS_POST) {
            $data = $_POST;
            $Model = M('project_claim');
            if ($data["id"]) {
                if ($Model->save($data) !== false) {
                    $this->success(L('_SUCCESS_UPDATE_'), Cookie('__forward__'));
                } else {
                    $this->error(L('_FAIL_UPDATE_'));
                }
            } else {
                if ($Model->add($data) !== false) {
                    $this->success("添加成功", Cookie('__forward__'));
                } else {
                    $this->error("添加失败");
                }
            }
        } else {
            $data = M("project_claim")->where(array("id" => $id))->find();
            $builder = new AdminConfigBuilder();
            $builder->title($act . "项目")
                ->keyId()
                ->keyText('name', "项目名称")
                ->keyTextArea('desc', "描述")
                ->keyMultiImage("pic1","图片")
                ->data($data)
                ->buttonSubmit()
                ->buttonBack()->display();
        }
    }


    /**
     * 自治金项目
     * @param int $page
     * @param int $r
     */
    public function zzxm($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 1);
        $model = M('zzjxm');
        $list = $model->where($map)->page($page, $r)->select();
        foreach ($list as $k => $v) {

			$list[$k]['qrcode'] = '/home/index/qrcode/type/xmbm/id/'.$v['id'];

        }
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';

        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $builder->title('自治金项目')
            ->buttonNew(U("editZzjxm"))
            ->setStatusUrl(U('setZzjxmStatus'))->buttonDelete()
            ->keyId()->keyText('title', '项目')->keyText('juwei', '居委')
            ->keyText('name', '联系人')
            ->keyText('tel', '联系方式')
			->keyImage('qrcode', '二维码', array("width" => 24, "height" => 24 ,"style"=>"border-radius:0;"))
            ->keyDoActionEdit("editZzjxm?id=###")
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setZzjxmStatus()
    {
        $ids = I('ids');
        $status = I('get.status', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetStatus('zzjxm', $ids, $status);
    }

    public function editZzjxm()
    {
        $id = I("id", 0, "intval");
        $act = $id ? "编辑" : "新增";

        if (IS_POST) {
            $data = $_POST;
            $Model = M('zzjxm');
            if ($data["id"]) {
                if ($Model->save($data) !== false) {
                    $this->success(L('_SUCCESS_UPDATE_'), Cookie('__forward__'));
                } else {
                    $this->error(L('_FAIL_UPDATE_'));
                }
            } else {
                if ($Model->add($data) !== false) {
                    $this->success("添加成功", Cookie('__forward__'));
                } else {
                    $this->error("添加失败");
                }
            }
        } else {
            $data = M("zzjxm")->where(array("id" => $id))->find();
            $builder = new AdminConfigBuilder();
            $data["group_str"] = $data["group"];
            $builder->title($act . "自治金项目")
                ->keyId()
                ->keyText('juwei', "居委")
                ->keyText('title', "主题")
                ->keyText('addr', "地址")
                ->keyText('name', "联系人")
                ->keyText('tel', "联系电话")
                ->keyText('group_str', "组织团体")
                ->keyTextArea('content', "内容")
                ->keySingleImage("pic", "图片")
                ->data($data)
                ->buttonSubmit()
                ->buttonBack()->display();
        }
    }


    /**
     * 自治项目报名信息
     */
    public function zzjxmbm($page = 1, $r = 10)
    {

        $state = I('get.state', 100, 'intval');
        //读取列表
        $map['ljz_data_xmbm.status'] = 1;
        if ($state != 100) {
            $map['ljz_data_xmbm.state'] = $state;
        }
        $model = M('data_xmbm');
        $list = $model->where($map)->field('ljz_data_xmbm.*,ljz_zzjxm.title as aname,ljz_zzjxm.juwei as ajw')->page($page, $r)->join('left join ljz_zzjxm on ljz_data_xmbm.aid = ljz_zzjxm.id')->order('ljz_data_xmbm.create_time desc')->select();
        $statemap = array("0" => "待审核", "1" => "报名成功", "2" => "已取消");
        foreach ($list as &$v) {
            $v['state_str'] = $statemap[$v['state']];
        }

        $select['title'] = "审核状态";
        $select['name'] = "state";
        $select['arrvalue'] = array(array("id" => "100", "value" => "全部"),
            array("id" => "0", "value" => "待审核"),
            array("id" => "1", "value" => "报名成功"),
            array("id" => "2", "value" => "已取消"));
        $selects[] = $select;
        $this->assign('selects', $selects);
        $this->assign('selectPostUrl', U("zzjxmbm"));


        $totalCount = $model->where($map)->count();
        $pager = new \Think\Page($totalCount, $r, $_REQUEST);
        $pager->setConfig('theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        $paginationHtml = $pager->show();
        $this->assign('pagination', $paginationHtml);
        $this->assign("list", $list);
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->display();
    }

    /**
     * 自治项目报名审核
     **/
    public function setZzjxmBmState()
    {
        $ids = I('id');
        $state = I('get.state', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetState('data_xmbm', $ids, $state);
    }

    /**
     * 删除自治金报名
     **/
    public function setBmStatus()
    {
        $ids = I('id');
        $state = I('get.status', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetStatus('data_xmbm', $ids, $state);
    }

    /**
     *公益城项目认领信息
     */
    public function gycrl($page = 1, $r = 10)
    {
        $aid = I('get.aid', 0, 'intval');
        $state = I('get.state', 100, 'intval');

        $projects = M("project_claim")->where(array("status"=>1))->select();
        if(!$aid){
            $aid=$_GET["aid"] =$projects[0]["id"];
        }
        $projectopt = array();
        foreach ($projects as $v){
            $projectopt[] = array("id"=>$v["id"],"value"=>$v["name"]);
        }
        //读取列表
        $map['ljz_data_gycrl.status'] = 1;
        $map['ljz_data_gycrl.aid'] = $aid;
        if ($state != 100) {
            $map['ljz_data_gycrl.state'] = $state;
        }
        $model = M('data_gycrl');
        $list = $model->where($map)->field('ljz_data_gycrl.*,ljz_project_claim.name as aname')->page($page, $r)->join('left join ljz_project_claim on ljz_data_gycrl.aid = ljz_project_claim.id')->order('ljz_data_gycrl.create_time desc')->select();
        $statemap = array("0" => "待审核", "1" => "审核通过", "-1" => "审核未通过");
        $rllxmap = array("1" => "个人认领", "2" => "单位认领");
        $sexmap = array("1" => "男", "2" => "女");
        foreach ($list as &$v) {
            $v['state_str'] = $statemap[$v['state']];
            $v["rllx_str"]=$rllxmap[$v["rllx"]];
            $map3["id"]=array("in",$v["rltype"]);
            $types = M("data_rltype")->where($map3)->select();
            $v["rltype_str"] = implode(",",array_column($types,"name"));
            if($v['qt']){
                if($v["rltype_str"]){
                    $v["rltype_str"].=",".$v['qt'];
                }else{
                    $v["rltype_str"]=$v['qt'];
                }
            }
            $v["sex_str"] =$sexmap[$v["sex"]];
            if($v["state"] !=0){
                $v["_hide"]=array("通过","不通过");
            }
        }
//        dump($list);exit;
        unset($v);

        $stateopt = array(array("id" => "100", "value" => "全部"),
            array("id" => "0", "value" => "待审核"),
            array("id" => "1", "value" => "审核通过"),
            array("id" => "2", "value" => "审核未通过"));

        $totalCount = $model->where($map)->count();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $builder = new AdminListBuilder();
        $builder->title("认领信息")
            ->setStatusUrl(U('setGycrlStatus'))->buttonDelete()
            ->setSelectPostUrl(U('gycrl'))->select("项目名称", "aid", "select", "", "", "", $projectopt)->select("审核状态", "state", "select", "", "", "", $stateopt)
            ->keyId();
//            ->keyText("rllx_str","认领类型")
//            ->keyText("dw","认领单位")
//            ->keyText("name","姓名")
//            ->keyText("mobile","联系电话")
//            ->keyTime("create_time","认领时间")
//            ->keyText("aname","项目名称")
//            ->keyText("sum","认领金额（元）")
//            ->keyText("rltype_str","认领项目")
//            ->keyText("idnum","身份证号")
//            ->keyText("state_str","认领审核状态")
        switch ($aid){
            case 13:
                $builder->keyText("name","姓名")->keyText("sex_str","性别")->keyText("mobile","联系方式")->keyText("idnum","身份证号");
                break;
            case 14:
                $builder->keyText("rllx_str","认领类型")->keyText("dw","单位名称")->keyText("name","姓名")->keyText("mobile","联系方式")->keyText("rltype_str","认领项目");
                break;
            case 15:
                $builder->keyText("rllx_str","认领类型")->keyText("dw","单位名称")->keyText("name","姓名")->keyText("mobile","联系方式")->keyText("sum","意向金额");
                break;
            case 17:
                $builder->keyText("dw","单位名称")->keyText("name","联系人")->keyText("mobile","联系方式");
                break;
            case 18:
                $builder->keyText("name","姓名")->keyText("sex_str","性别")->keyText("mobile","联系方式");
                break;
            case 20:
                $builder->keyText("name","姓名")->keyText("mobile","联系方式")->keyText("rltype_str","认领项目");
                break;
            case 28:
                $builder->keyText("rllx_str","认领类型")->keyText("dw","单位名称")->keyText("name","姓名")->keyText("mobile","联系方式");
                break;
        }


        $builder->keyTime("create_time","认领时间")
            ->keyText("state_str","认领审核状态")
            ->keyDoAction("setGycrlState?ids=###&state=1", "通过 ", "操作", array('class' => 'ajax-get'))
            //->keyDoAction("setGycrlState2?ids=###&state=-1", "不通过", "操作2", array('class' => 'ajax-get'))
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 公益城项目评价
     */
    public function gycpj($page=1,$r = 10){
        $aid = I('get.aid', 0, 'intval');
        $state = I('get.state', 100, 'intval');
        $_GET["state"] = $state;

        $projects = M("project_claim")->where(array("status"=>1))->select();
        $projectopt = array(array("id"=>0,"value"=>"全部"));
        foreach ($projects as $v){
            $projectopt[] = array("id"=>$v["id"],"value"=>$v["name"]);
        }
        //读取列表
        $map['status'] = 1;
        if($aid) {
            $map['aid'] = $aid;
        }
        if ($state != 100) {
            $map['state'] = $state;
        }
        $model = M('data_pj');
        $list = $model->where($map)->page($page, $r)->order('createtime desc')->select();
        $statemap = array("0" => "待审核", "1" => "审核通过", "-1" => "审核未通过");
        $pjmap = array("1" => "非常满意", "2" => "满意", "3" => "一般","4"=>"不够满意");
        foreach ($list as &$v) {
            $v['state_str'] = $statemap[$v['state']];
            $v["pj_str"]=$pjmap[$v["pj"]];
            $v["aname"] = M("project_claim")->getFieldById($v["aid"],"name");
        }
//        dump($list);exit;
        unset($v);

        $stateopt = array(array("id" => "100", "value" => "全部"),
            array("id" => "0", "value" => "待审核"),
            array("id" => "1", "value" => "审核通过"),
            array("id" => "2", "value" => "审核未通过"));

        $totalCount = $model->where($map)->count();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $builder = new AdminListBuilder();
        $builder->title("项目评价")
            ->setStatusUrl(U('setGycpjStatus'))->buttonDelete()
            ->ajaxButton(U("setGycpjState"),array("state"=>1),"审核通过")
            ->ajaxButton(U("setGycpjState"),array("state"=>-1),"审核不通过")
            ->setSelectPostUrl(U('gycpj'))->select("项目名称", "aid", "select", "", "", "", $projectopt)->select("审核状态", "state", "select", "", "", "", $stateopt)
            ->keyId()
            ->keyText("aname","项目名称")
            ->keyText("pj_str","评价")
            ->keyText("content","心声")
            ->keyTime("createtime","认领时间")
            ->keyText("state_str","评价审核状态")
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 公益城项目评价审核
     **/
    public function setGycpjState()
    {
        $ids = I('ids');
        $state = I('get.state', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetState('data_pj', $ids, $state);
    }

    /**
     * 公益城项目评价删除
     **/
    public function setGycpjStatus()
    {
        $ids = I('ids');
        $state = I('get.status', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetStatus('data_pj', $ids, $state);
    }


    /**
     * 公益城项目认领审核
     **/
    public function setGycrlState()
    {
        $ids = I('ids');
        $state = I('get.state', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetState('data_gycrl', $ids, $state);
    }

    /**
     * 公益城项目认领删除
     **/
    public function setGycrlStatus()
    {
        $ids = I('ids');
        $state = I('get.status', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetStatus('data_gycrl', $ids, $state);
    }

//
//    public function partyMemberList($page = 1, $r = 10)
//    {
//        //读取列表
//        $map = array('status' => 1);
//        $model = M('party_member');
//        $list = $model->where($map)->page($page, $r)->select();
//
//        $sexmap = array("1" => "男", "2" => "女");
//        foreach ($list as &$v) {
//            $v['sex'] = $sexmap[$v['sex']];
//        }
//
//        $totalCount = $model->where($map)->count();
//        //显示页面
//        $builder = new AdminListBuilder();
//        $attr['class'] = 'btn ajax-post';
//        $attr['target-form'] = 'ids';
//
//        $builder->title('党员名单')
//            ->setStatusUrl(U('setPartyMemberStatus'))->buttonDelete()
//            ->keyId()->keyText('name', '姓名')->keyText('idnum', '身份证号')->keyText('sex', '性别')->keyText('tel', '电话')
//            ->data($list)
//            ->pagination($totalCount, $r)
//            ->display();
//    }
//
//    public function setPartyMemberStatus()
//    {
//        $ids = I('ids');
//        $status = I('get.status', 0, 'intval');
//        $builder = new AdminListBuilder();
//        $builder->doSetStatus('party_member', $ids, $status);
//    }


    public function issue()
    {
        //显示页面
        $builder = new AdminTreeListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';
        $attr1 = $attr;
        $attr1['url'] = $builder->addUrlParam(U('setWeiboTop'), array('top' => 1));
        $attr0 = $attr;
        $attr0['url'] = $builder->addUrlParam(U('setWeiboTop'), array('top' => 0));
        $tree = D('Issue/Issue')->getTree(0, 'id,title,sort,pid,status');
        $builder->title(L('_ISSUE_MANAGE_'))
            ->buttonNew(U('Issue/add'))
            ->data($tree)
            ->display();
    }

    public function add($id = 0, $pid = 0)
    {
        if (IS_POST) {
            if ($id != 0) {
                $issue = $this->issueModel->create();
                if ($this->issueModel->save($issue)) {
                    $this->success(L('_SUCCESS_EDIT_'));
                } else {
                    $this->error(L('_FAIL_EDIT_'));
                }
            } else {
                $issue = $this->issueModel->create();
                if ($this->issueModel->add($issue)) {

                    $this->success(L('_SUCCESS_ADD_'));
                } else {
                    $this->error(L('_FAIL_ADD_'));
                }
            }


        } else {
            $builder = new AdminConfigBuilder();
            $issues = $this->issueModel->select();
            $opt = array();
            foreach ($issues as $issue) {
                $opt[$issue['id']] = $issue['title'];
            }
            if ($id != 0) {
                $issue = $this->issueModel->find($id);
            } else {
                $issue = array('pid' => $pid, 'status' => 1);
            }


            $builder->title(L('_CATEGORY_ADD_'))->keyId()->keyText('title', L('_TITLE_'))->keySelect('pid', L('_FATHER_CLASS_'), L('_FATHER_CLASS_SELECT_'), array('0' => L('_TOP_CLASS_')) + $opt)
                ->keyStatus()->keyCreateTime()->keyUpdateTime()
                ->data($issue)
                ->buttonSubmit(U('Issue/add'))->buttonBack()->display();
        }

    }

    public function issueTrash($page = 1, $r = 20, $model = '')
    {
        $builder = new AdminListBuilder();
        $builder->clearTrash($model);
        //读取微博列表
        $map = array('status' => -1);
        $model = $this->issueModel;
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        //显示页面

        $builder->title(L('_ISSUE_TRASH_'))
            ->setStatusUrl(U('setStatus'))->buttonRestore()->buttonClear('Issue/Issue')
            ->keyId()->keyText('title', L('_TITLE_'))->keyStatus()->keyCreateTime()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function operate($type = 'move', $from = 0)
    {
        $builder = new AdminConfigBuilder();
        $from = D('Issue')->find($from);

        $opt = array();
        $issues = $this->issueModel->select();
        foreach ($issues as $issue) {
            $opt[$issue['id']] = $issue['title'];
        }
        if ($type === 'move') {

            $builder->title(L('_CATEGORY_MOVE_'))->keyId()->keySelect('pid', L('_FATHER_CLASS_'), L('_FATHER_CLASS_SELECT_'), $opt)->buttonSubmit(U('Issue/add'))->buttonBack()->data($from)->display();
        } else {

            $builder->title(L('_CATEGORY_COMBINE_'))->keyId()->keySelect('toid', L('_CATEGORY_T_COMBINE_'), L('_CATEGORY_T_COMBINE_SELECT_'), $opt)->buttonSubmit(U('Issue/doMerge'))->buttonBack()->data($from)->display();
        }

    }

    public function doMerge($id, $toid)
    {
        $effect_count = D('IssueContent')->where(array('issue_id' => $id))->setField('issue_id', $toid);
        D('Issue')->where(array('id' => $id))->setField('status', -1);
        $this->success(L('_SUCCESS_CATEGORY_COMBINE_') . $effect_count . L('_CONTENT_GE_'), U('issue'));
        //TODO 实现合并功能 issue
    }

    public function contents($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 1);
        $model = M('IssueContent');
        $list = $model->where($map)->page($page, $r)->select();
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';


        $builder->title(L('_CONTENT_MANAGE_'))
            ->setStatusUrl(U('setIssueContentStatus'))->buttonDisable('', L('_AUDIT_UNSUCCESS_'))->buttonDelete()
            ->keyId()->keyLink('title', L('_TITLE_'), 'Issue/Index/issueContentDetail?id=###')->keyUid()->keyCreateTime()->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function verify($page = 1, $r = 10)
    {
        //读取列表
        $map = array('status' => 0);
        $model = M('IssueContent');
        $list = $model->where($map)->page($page, $r)->select();
        unset($li);
        $totalCount = $model->where($map)->count();

        //显示页面
        $builder = new AdminListBuilder();
        $attr['class'] = 'btn ajax-post';
        $attr['target-form'] = 'ids';


        $builder->title(L('_CONTENT_AUDIT_'))
            ->setStatusUrl(U('setIssueContentStatus'))->buttonEnable('', L('_AUDIT_SUCCESS_'))->buttonDelete()
            ->keyId()->keyLink('title', L('_TITLE_'), 'Issue/Index/issueContentDetail?id=###')->keyUid()->keyCreateTime()->keyStatus()
            ->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    /**
     * 资源清单
     * 1居民区与驻区单位联建清单 2合庆党建服务站点清单 3合庆街道活动场地清单 4“合庆公益城”驻区单位资源清单 5点亮心愿\n社区单位认领心愿 6公益进工地认领清单 7白领课堂 8社会组织服务清单 9合庆街道自治金项目清单 10社区文化活动清单 11金融服务进社区清单 12群团服务项目清单 13社区事务受理事项清单 14拥军优属服务项目清单
     */
    public function zyqd($page = 1, $r = 10)
    {
        $type = I('type', 15, "intval");
        $_GET['type'] = $type;
        //读取列表
        $map['type'] = $type;
        $map['status'] = 1;
        $model = M('data_zyqd');
        $list = $model->where($map)->page($page, $r)->select();
        $totalCount = $model->where($map)->count();

        $typeopt = array(
//            array("id" => 1, "value" => "居民区与驻区单位联建清单"),
//            array("id" => 2, "value" => "合庆党建服务站点清单"),
//            array("id" => 3, "value" => "合庆街道活动场地清单"),
//            array("id" => 4, "value" => "“合庆公益城”驻区单位资源清单"),
//            array("id" => 5, "value" => "点亮心愿 社区单位认领心愿"),
//            array("id" => 6, "value" => "公益进工地认领清单"),
//            array("id" => 7, "value" => "白领课堂"),
//            array("id" => 8, "value" => "社会组织服务清单"),
//            array("id" => 9, "value" => "合庆街道自治金项目清单"),
//            array("id" => 10, "value" => "社区文化活动清单"),
//            array("id" => 11, "value" => "金融服务进社区清单"),
//            array("id" => 12, "value" => "群团服务项目清单"),
//            array("id" => 13, "value" => "社区事务受理事项清单"),
//            array("id" => 14, "value" => "拥军优属服务项目清单"),
            array("id" => 15, "value" => "村居党建服务站"),
            array("id" => 16, "value" => "基层党组织"),
            array("id" => 17, "value" => "区域化共建单位")
        );

        $titles = array_combine(array_column($typeopt, "id"), array_column($typeopt, "value"));
        $title = $titles[$type];
        //显示页面
        $builder = new AdminListBuilder();
        $builder->title($title)
            ->buttonNew(U("editZy") . "&type=" . $type)
            ->buttonDelete(U('setZyStatus'))
            ->setSelectPostUrl(U("zyqd"))
            ->select("资源类型", "type", "select", "", "", "", $typeopt)
            ->data($list)
            ->pagination($totalCount, $r);

        switch ($type) {
//            case 1:
//                $builder->keyId()->keyText('name', '联建单位名称')->keyText('dzzmc', '居民区党组织名称');
//                break;
//            case 2:
//                $builder->keyId()->keyText('name', '站点名称')->keyText('addr', '地址')->keyText('tel', '联系方式');
//                break;
//            case 3:
//                $builder->keyId()->keyText('name', '场地名称')->keyText('num', '容纳人数')->keyText('addr', '地址')->keyText('tel', '联系方式');
//                break;
//            case 4:
//                $builder->keyId()->keyText('dwmc', '单位名称')->keyText('name', '项目名称')->keyText('content', '资源内容')->keyText('bz', '备注');
//                break;
//            case 5:
//                $builder->keyId()->keyText('dwmc', '认领单位')->keyText('age', '年龄')->keyText('sex', '性别')->keyText("content", "家庭困难情况")->keyText("name", "心愿内容");
//                break;
//            case 6:
//                $builder->keyId()->keyText('lb', '类别')->keyText('dwmc', '认领单位')->keyText('name', '公益形式');
//                break;
//            case 7:
//                $builder->keyId()->keyText('name', '课程名称')->keyText('content', '课程简介')->keyText('dwmc', '认领单位')->keyText('sj', '时间')->keyText('addr', '课程地址')->keyText('sz', '授课老师/师资力量介绍');
//                break;
//            case 8:
//                $builder->keyId()->keyText('dwmc', '社会组织名称')->keyText('content', '服务项目')->keyText('name', '负责人')->keyText('addr', '地址')->keyText('tel', '联系方式');
//                break;
//            case 9:
//                $builder->keyId()->keyText('jw', '居委')->keyText('name', '项目名称')->keyText('je1', '项目金额')->keyText('je2', '街道财政')->keyText('je3', '汇丰-交行资助项目');
//                break;
//            case 10:
//                $builder->keyId()->keyText('name', '活动名称')->keyText('content', '简介')->keyText('sj', '时间')->keyText('bz', '参加对象')->keyText('num', '参加人数')->keyText('addr', '地址');
//                break;
//            case 11:
//                $builder->keyId()->keyText('jw', '居民区名称')->keyText('dwmc', '金融单位')->keyText('sj', '活动时间')->keyText('name', '名称')->keyText('num', '参加人数')->keyText('month', '月份');
//                break;
//            case 12:
//                $builder->keyId()->keyText('sj', '时间')->keyText('name', '项目')->keyText('zb', '主办')->keyText('xb', '协办');
//                break;
//            case 13:
//                $builder->keyId()->keyText('name', '事项名称')->keyText('dwmc', '单位名称');
//                break;
//            case 14:
//                $builder->keyId()->keyText('name', '项目')->keyText('sj', '开展时间')->keyText('addr', '地点')->keyText('xb', '参与');
//                break;
            case 15:
                $builder->keyId()->keyText('name', '站点名称')->keyText('addr', '地址')->keyText('tel', '联系方式');
                break;
            case 16:
                $builder->keyId()->keyText('name', '站点名称')->keyText('addr', '地址')->keyText('tel', '联系方式');
                break;
            case 17:
                $builder->keyId()->keyText('name', '站点名称')->keyText('addr', '地址')->keyText('tel', '联系方式');
                break;
        }
        $builder->keyDoActionEdit('editZy?id=###');
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $builder->display();
    }

    public function setZyStatus()
    {
        $ids = I('ids');
        $status = I('get.status', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetStatus('data_zyqd', $ids, $status);
    }

    /**
     * 编辑资源
     */
    public function editZy()
    {
        $id = I('id', 0, 'intval');
        $type = I('type', 1, 'intval');
        $is_edit = $id ? 1 : 0;
        $title = $is_edit ? "编辑" : "添加";
        if (IS_POST) {
            $data = $_POST;
            $data['type'] = $data['type_wrap'];
            $MODEL = M('data_zyqd');
            if ($data["id"]) {
                if ($MODEL->save($data) !== false) {
                    $this->success(L('_SUCCESS_UPDATE_'), Cookie('__forward__'));
                } else {
                    $this->error(L('_FAIL_UPDATE_'));
                }
            } else {
                if ($MODEL->add($data) !== false) {
                    $this->success("添加成功", Cookie('__forward__'));
                } else {
                    $this->error("添加失败");
                }
            }

        } else {
            $data = array();
            if ($is_edit) {
                $data = M('data_zyqd')->where(array('id' => $id))->find();
                $type = $data['type'];
            }

            $typeot = array(
//                1 => "居民区与驻区单位联建清单",
//                2 => "合庆党建服务站点清单",
//                3 => "合庆街道活动场地清单",
//                4 => "“合庆公益城”驻区单位资源清单",
//                5 => "点亮心愿 社区单位认领心愿",
//                6 => "公益进工地认领清单",
//                7 => "白领课堂",
//                8 => "社会组织服务清单",
//                9 => "合庆街道自治金项目清单",
//                10 => "社区文化活动清单",
//                11 => "金融服务进社区清单",
//                12 => "群团服务项目清单",
//                13 => "社区事务受理事项清单",
//                14 => "拥军优属服务项目清单"
                15 => "村居党建服务站",
                16 => "基层党组织",
                17 => "区域化共建单位"
            );
            $title .= $typeot[$type];
            $data['type_wrap'] = $type;

            $builder = new AdminConfigBuilder;
            $builder->title($title);
            $builder->keyId();

            switch ($type) {
//                case 1:
//                    $builder->keyText('name', '联建单位名称')->keyText('dzzmc', '居民区党组织名称');
//                    break;
//                case 2:
//                    $builder->keyText('name', '站点名称')->keyBDMap('addr|lng|lat', '地址')->keyText('tel', '联系方式');
//                    break;
//                case 3:
//                    $builder->keyText('name', '场地名称')->keyText('num', '容纳人数')->keyBDMap('addr|lng|lat', '地址')->keyText('tel', '联系方式');
//                    break;
//                case 4:
//                    $builder->keyText('dwmc', '单位名称')->keyText('name', '项目名称')->keyTextArea('content', '资源内容')->keyText('bz', '备注');
//                    break;
//                case 5:
//                    $builder->keyText('dwmc', '认领单位')->keyText('age', '年龄')->keyText('sex', '性别')->keyTextArea("content", "家庭困难情况")->keyText("name", "心愿内容");
//                    break;
//                case 6:
//                    $builder->keyText('lb', '类别')->keyText('dwmc', '认领单位')->keyText('name', '公益形式');
//                    break;
//                case 7:
//                    $builder->keyText('name', '课程名称')->keyTextArea('content', '课程简介')->keyText('dwmc', '认领单位')->keyText('sj', '时间')->keyBDMap('addr|lng|lat', '课程地址')->keyText('sz', '授课老师/师资力量介绍');
//                    break;
//                case 8:
//                    $builder->keyText('dwmc', '社会组织名称')->keyTextArea('content', '服务项目')->keyText('name', '负责人')->keyBDMap('addr|lng|lat', '地址')->keyText('tel', '联系方式');
//                    break;
//                case 9:
//                    $builder->keyText('jw', '居委')->keyText('name', '项目名称')->keyText('je1', '项目金额')->keyText('je2', '街道财政')->keyText('je3', '汇丰-交行资助项目');
//                    break;
//                case 10:
//                    $builder->keyText('name', '活动名称')->keyTextArea('content', '简介')->keyText('sj', '时间')->keyText('bz', '参加对象')->keyText('num', '参加人数')->keyBDMap('addr|lng|lat', '地址');
//                    break;
//                case 11:
//                    $builder->keyText('jw', '居民区名称')->keyText('dwmc', '金融单位')->keyText('sj', '活动时间')->keyText('name', '名称')->keyText('num', '参加人数')->keyText('month', '月份');
//                    break;
//                case 12:
//                    $builder->keyText('sj', '时间')->keyText('name', '项目')->keyText('zb', '主办')->keyText('xb', '协办');
//                    break;
//                case 13:
//                    $builder->keyText('name', '事项名称')->keyText('dwmc', '单位名称');
//                    break;
//                case 14:
//                    $builder->keyText('name', '项目')->keyText('sj', '开展时间')->keyBDMap('addr|lng|lat', '地点')->keyText('xb', '参与');
//                    break;
                case 15:
                    $builder->keyText('name', '站点名称')->keyBDMap('addr|lng|lat', '地址')->keyText('tel', '联系方式');
                    break;
                case 16:
                    $builder->keyText('name', '站点名称')->keyBDMap('addr|lng|lat', '地址')->keyText('tel', '联系方式');
                    break;
                case 17:
                    $builder->keyText('name', '站点名称')->keyBDMap('addr|lng|lat', '地址')->keyText('tel', '联系方式');
                    break;
            }
            $builder->keyHidden('type_wrap', "")
                ->buttonSubmit()
                ->buttonBack()
                ->data($data)
                ->display();
        }

    }


    /**
     * 需求传声筒
     */
    public function xqcst($xqf=1,$page=1,$r=10){
        //读取列表
        $map["xqf"] =$xqf;
        $map["status"] =1;
        $model = M('data_xq');
        $list = $model->where($map)->page($page, $r)->select();
        $xqfmap = array("1"=>"个人","2"=>"单位");
        $sexmap = array("1"=>"男","2"=>"女");
        $xqlbmap = array("1"=>"公益帮扶类","2"=>"家政服务类","3"=>"政策咨询类","4"=>"党务党建类");
        foreach ($list as &$v) {
            $v["xqf_str"] = $xqfmap[$v["xqf"]];
            $v["xqlb_str"] = $xqlbmap[$v["xqf"]];
            $v["sex_str"]=$sexmap[$v["sex"]];
        }
        unset($v);
        $totalCount = $model->where($map)->count();

        $xqfopt = array(array("id"=>1,"value"=>"个人"),array("id"=>2,"value"=>"单位"));
        //显示页面
        $builder = new AdminListBuilder();
        $builder->title('需求传声筒')
            ->setStatusUrl(U('setXqStatus'))->buttonDelete()
            ->setSelectPostUrl(U('xqcst'))
            ->select("需求方", "xqf", "select", "", "", "", $xqfopt);
        if($xqf ==1){//个人
            $builder->keyId()
                ->keyText('name', '姓名')
                ->keyText("sex_str", '性别')
                ->keyText('age', '年龄')
                ->keyText('dzzname', '所在党组织')
                ->keyText('tel', '联系电话')
                ->keyText('xqlb', '需求类别')
                ->keyTime('enddate', '需求时效')
                ->keyText('content', '需求内容');
        }else{//单位
            $builder->keyId()
                ->keyText('dw', '单位名称')
                ->keyText("name", '联系人姓名')
                ->keyText('tel', '联系电话')
                ->keyText('xqlb', '需求类别')
                ->keyTime('enddate', '需求时效')
                ->keyText('content', '需求内容');
        }


           $builder->data($list)
            ->pagination($totalCount, $r)
            ->display();
    }

    public function setXqStatus(){
        $ids = I('ids');
        $status = I('get.status', 0, 'intval');
        $builder = new AdminListBuilder();
        $builder->doSetStatus('data_xq', $ids, $status);
    }

}

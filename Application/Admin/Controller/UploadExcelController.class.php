<?php


namespace Admin\Controller;

use Think\Controller;
use Think\Page;
use Think\Page2;
use Think\Storage;

class UploadExcelController extends AdminController
{
	
    public function index(){
        redirect(U('home'));
    }

    public function home()
    {
        $this->display();
    }


    public function tongbu($page=0,$row = 100)
    {
        $start =  ($page-1)*$row;
        $map['idnum'] = array("gt",0);
        $map['dzz']  =  array("gt",0);
        $addnum = 0;
        $savenum = 0;
        $list = M("party_member_new")->where($map)->field('id,sszb,jw,mdd,bldjsj,cgsy,mgyy,tc,zw,zt,sbd,mzpy,cgbldj,mgzzsh,birthyear,ydzz,birthday,chongfu',true)->limit($start,$row)->select();
        foreach ($list as $k=>$v){
            $map2['idnum'] = $v['idnum'];
            $info = M("party_member")->where($map2)->find();
            if($info['id']){
                $do = M("party_member")->where($map2)->save($v);
                $savenum++;
            }else{
                $do = M("party_member")->where($map2)->add($v);
                $addnum++;
            }
        }
        $page++;
        $allnum = $savenum+$addnum;
        $this->assign("allnum", $allnum);
        $this->assign("addnum", $addnum);
        $this->assign("savenum", $savenum);
        $this->assign("page", $page);

        $this->display();
    }

    public function excelupload($type = '',$index=0,$groupid=0)
    {
        header("Content-Type:text/html;charset=utf-8");

        $mapArray['party_member'] = "党员信息";
        $mapArray['mgzzsh'] = "免过组织生活";
        $mapArray['bldj'] = "因私出国（境）保留党籍党员名册";
        $mapArray['sbd'] = "党员“双报到”登记表";
        $mapArray['mzpy'] = "民主评议";
		$mapArray['example'] = "党员基本信息汇总表";

		$tableName= $mapArray[$type];

		$this->assign("tableName", $tableName);
		
        if ($_POST) {

            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 314572813456;// 设置附件上传大小
            $upload->exts = array('xls', 'xlsx', 'csv');// 设置附件上传类
            $upload->savePath = '/Excel/'; // 设置附件上传目录
            // 上传文件
            $info = $upload->uploadOne($_FILES['files']);

            $filename = './Uploads' . $info['savepath'] . $info['savename'];
            $exts = $info['ext'];
            if (!$info) {// 上传错误提示错误信息
                $info = $upload->getError();
                $this->assign("end", $info);
            } else {// 上传成功
//                $data = $info;
//                $data['path'] = '/Uploads' . $info['savepath'];
//                $data['create_time'] = time();
//
//                //上传信息存入数据库
//                $addinfo = M('excel')->add($data);
                vendor('PHPExcel.PHPExcel');
                $phpexcel = new \phpexcel();
                if ($exts == "xls") {
                    $do = $this->import_excelxls($filename, $type,$index,$groupid);
                    //dump($do);
//                    dump($type);
                    $this->assign("end", $this->showResult($do));
                } elseif ($exts == "xlsx") {
                    $do = $this->import_excelxlsx($filename, $type,$index,$groupid);
                    //dump($do);
//                    dump($type);
                    $this->assign("end", $this->showResult($do));
                }
                //$this->assign("end", '上传完毕');
                $this->assign("type", $type);
            }

            $this->display();

        } else {
            $this->assign("type", $type);
            $this->display();

        }
    }

    public function showResult($do){
        if ($do['result'] == '上传成功'){
            $stringRe = $do['result'].'('.'更新成功 '.count($do['saveSuccess']) .' 条,'.'更新失败 '.count($do['saveFaild']) .' 条,'.'添加成功 '.count($do['addSuccess']) .' 条,'.'添加失败 '.count($do['addFaild']) .' 条,'.')';
        }else{
            $stringRe = $do['result'];
        }

        return $stringRe;
        //return M()->getlastsql();
    }

    public function import_excelxls($filename, $type = "",$index=0,$groupid=0)
    {
        if (!file_exists($filename)) {
            exit("文件内容不能为空!");
        } else {
            vendor("PHPExcel.Reader.Excel5");
            $phpexcelReader = new \PHPExcel_Reader_Excel5();
            $phpexcel = $phpexcelReader->load($filename);
            // 获取工作表(及当前活动的sheet)
            $currentSheet = $phpexcel->getSheet($index);
            // 获取总列数
            $allColumn = $currentSheet->getHighestColumn();
            // 获取总行数
            $allRow = $currentSheet->getHighestRow();
            //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
            for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
                //从哪列开始，A表示第一列
                for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                    //数据坐标
                    $address = $currentColumn . $currentRow;
                    //读取到的数据，保存到数组$arr中
                    $arr[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();

                    if(is_object($currentSheet->getCell($address)->getValue())) {
                        $arr[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue()->__toString();
                    }

                }
            }

            return $this->switchtable($type,$arr,$groupid);

            //return M()->getlastsql();
        }

    }

    public function import_excelxlsx($filename, $type = "",$index=1,$groupid=0)
    {
        if (file_exists($filename)) {
            vendor("PHPExcel.Reader.Excel2007");
            $phpexcelReader = new \PHPExcel_Reader_Excel2007();
            $phpexcel = $phpexcelReader->load($filename);
            // 获取工作表(及当前活动的sheet)
            $currentSheet = $phpexcel->getSheet($index);
            // 获取总列数
            $allColumn = $currentSheet->getHighestColumn();
            // 获取总行数
            $allRow = $currentSheet->getHighestRow();
            //循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
            for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
                //从哪列开始，A表示第一列
                for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                    //数据坐标
                    $address = $currentColumn . $currentRow;
                    //读取到的数据，保存到数组$arr中
                    $arr[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue();
                    if(is_object($currentSheet->getCell($address)->getValue())) {
                        $arr[$currentRow][$currentColumn] = $currentSheet->getCell($address)->getValue()->__toString();
                    }
                }
            }
            return $this->switchtable($type,$arr,$groupid);
            //return M()->getlastsql();
        } else {
            exit("文件不存在");
        }

    }

    private function switchtable($type,$arr,$groupid=0){
        $mapArray['party_member'] = "党员名册";
        $mapArray['mgzzsh'] = "免过组织生活";
        $mapArray['bldj'] = "因私出国（境）保留党籍党员名册";
        $mapArray['sbd'] = "党员“双报到”登记表";
        $mapArray['mzpy'] = "民主评议";
		$mapArray['example'] = "党员基本信息汇总表";
        if (strstr($arr[1]['A'],$mapArray[$type])){

        }else{
            //报错的
            $result['result'] = '文件不匹配';
            return $result;

        }

        switch ($type)
        {
            case "party_member" :
                $do =  $this->parse_partymember($arr,$type);break;
                break;
            case "mgzzsh" :
                $do =  $this->parse_mgzzsh($arr,$type);break;
                break;
            case "bldj" :
                $do =  $this->parse_bldj($arr,$type);break;
            case "sbd" :
                $do =  $this->parse_sbd($arr,$type);break;
            case "mzpy" :
                $do =  $this->parse_mzpy($arr,$type);break;
			case "example" :
                $do =  $this->parse_example($arr,$type,$groupid);break;
            
            default:
                break;
        }

        $sucArr = $do['array'];

        $addSuccess = array();
        $saveSuccess = array();
        $addFaild = array();
        $saveFaild = array();
        $count = count($sucArr);

        $k=0;$j=0;$m=0;$n=0;
        for ($i=0; $i < $count+1; $i++) {
            if ($sucArr[$i]['sucessType'] == 'saveSucess'){
                $saveSuccess[$k++] = $sucArr[$i];
            }elseif($sucArr[$i]['sucessType'] == 'saveFaild'){
                $saveFaild[$j++] = $sucArr[$i];
            }elseif ($sucArr[$i]['sucessType'] == 'addSucess'){
                $addSuccess[$m++] = $sucArr[$i];
            }elseif ($sucArr[$i]['sucessType'] == 'addFaild'){
                $addFaild[$n++] = $sucArr[$i];
            }
        }

        $do['saveFaild'] = $saveFaild;
        $do['addFaild'] = $addFaild;
        $do['saveSuccess'] = $saveSuccess;
        $do['addSuccess'] = $addSuccess;

        return $do;
    }

    public function databaseSave($table,$data,$where){
        if ($table->where($where)->save($data) !== false){
            $data['sucessType'] = 'saveSucess';
        }else{
            $data['sucessType'] = 'saveFaild';
        }
        return $data;
    }

    public function databaseAdd($table,$data){
        if ($table->data($data)->add() > 0){
            $data['sucessType'] = 'addSucess';
        }else{
            $data['sucessType'] = 'addFaild';

        }
        return $data;
    }
	//
    private function parse_example($arr, $type,$groupid=0)
    {
    	if($type = 'example'){
    		$type = 'party_member_new';
    	}	
        $sucessArray = array();

        $sexopt = array('男'=>1,"女"=>2);
        $count = count($arr);
        $table = M($type);
        for ($i=4; $i < $count+1; $i++) {
            if ($arr[$i]['A'] == null){
                continue;
            }
	
			$xxx="a序号	b姓名	c所在党支部	d公民身份证号	e性别	f民族	g出生日期	h学历	i人员类别	 j加入党组织日期	k转为正式党员日期	
			l工作岗位	m手机号	n固定电话".	
			"o家庭住址	p党籍状态	q是否为失联党员	r失去联系日期	s是否为流动党员	t外出流向	u备注";
            if($groupid){
                $data['dzz'] = $groupid;
            }
			$data['name'] = $arr[$i]['B'];
			if($arr[$i]['C']){
				$data['dzzmc'] = $arr[$i]['C'];
				$data['idnum'] = $arr[$i]['D'];
				$data['sex'] = $sexopt[$arr[$i]['E']];
				$data['mz'] = $arr[$i]['F'];
				$data['birth'] = excel2Time($arr[$i]['G']);
				$data['whcd'] = $arr[$i]['H'];
				$data['dylx'] = $arr[$i]['I'];
				$data['rddate'] = excel2Time($arr[$i]['J']);
				$data['zsdydate'] = excel2Time($arr[$i]['K']);
				$data['gzdw'] = $arr[$i]['L'];
				$data['phone'] = $arr[$i]['M'];
				$data['tel'] = $arr[$i]['N'];
				$data['addr'] = $arr[$i]['O'];
				$data['djzt'] = $arr[$i]['P'];
				$data['sldy'] = $arr[$i]['Q'];
				$data['slrq'] = excel2Time($arr[$i]['R']);
				$data['lddy'] = $arr[$i]['S'];
				$data['wclx'] = $arr[$i]['T'];
				$data['bz'] = $arr[$i]['U'];
			}else{
				$data['dzzmc'] = $arr[$i]['D'];
				$data['idnum'] = $arr[$i]['E'];
				$data['sex'] = $sexopt[$arr[$i]['F']];
				$data['mz'] = $arr[$i]['G'];
				$data['birth'] =   excel2Time($arr[$i]['H']);
				$data['whcd'] = $arr[$i]['I'];
				$data['dylx'] = $arr[$i]['J'];
				$data['rddate'] = excel2Time($arr[$i]['k']);
				$data['zsdydate'] = excel2Time($arr[$i]['L']);
				$data['gzdw'] = $arr[$i]['M'];
				$data['phone'] = $arr[$i]['N'];
				$data['tel'] = $arr[$i]['O'];
				$data['addr'] = $arr[$i]['P'];
				$data['djzt'] = $arr[$i]['Q'];
				$data['sldy'] = $arr[$i]['R'];
				$data['slrq'] = excel2Time($arr[$i]['S']);
				$data['lddy'] = $arr[$i]['T'];
				$data['wclx'] = $arr[$i]['U'];
				$data['bz'] = $arr[$i]['V'];
			}
			
            $data['dymc'] = 1;

            $condition['idnum'] = $data['idnum'];
			if($data['name'] && $data['idnum'] && $data['dzzmc']){
                $data['dzz'] = dzznameGetId($data['dzzmc']);
	            if($table->where($condition)->find()){
	                array_push($sucessArray,$this->databaseSave($table,$data,$condition));
	            }else{
	                array_push($sucessArray,$this->databaseAdd($table,$data));
	            }
			}
        }
        $result['array'] = $sucessArray;
        $result['result'] = '上传成功';
        return $result;
    }


    private function parse_partymember($arr, $type)
    {
        $sucessArray = array();

        $sexopt = array('男'=>1,"女"=>2);
        $count = count($arr);
        $table = M($type);
        for ($i=3; $i < $count+1; $i++) {
            if ($arr[$i]['A'] == null){
                continue;
            }
            $data['name'] = $arr[$i]['B'];
            $data['sex'] = $sexopt[$arr[$i]['C']];
            $data['mz'] = $arr[$i]['D'];
            $data['birth'] = $arr[$i]['E'];
            $data['idnum'] = $arr[$i]['F'];
            $data['whcd'] = $arr[$i]['G'];
            $data['dylx'] = $arr[$i]['H'];
            $data['rddate'] = $arr[$i]['I'];
            $data['zt'] = $arr[$i]['J'];
            $data['addr'] = $arr[$i]['K'];
            $data['tel'] = $arr[$i]['L'];
            $data['sszb'] = $arr[$i]['M'];
            $data['bz'] = $arr[$i]['N'];
            $data['dymc'] = 1;
            $data['dzzmc'] = trim(str_replace('党员名册','',$arr[1]['A']));

            $condition['idnum'] = $data['idnum'];

            if($table->where($condition)->find()){
                array_push($sucessArray,$this->databaseSave($table,$data,$condition));
            }else{
                array_push($sucessArray,$this->databaseAdd($table,$data));
            }
        }
        $result['array'] = $sucessArray;
        $result['result'] = '上传成功';
        return $result;
    }

    /**
     * 免过组织生活
     */
    private function parse_mgzzsh($arr, $type){
        $sucessArray = array();

        $sexopt = array('男'=>1,"女"=>2);
        $count = count($arr);
        $table = M('party_member');
        for ($i=3; $i < $count+1; $i++) {
            if ($arr[$i]['A'] == null){
                continue;
            }
            $data['name'] = $arr[$i]['B'];
            $data['sex'] = $sexopt[$arr[$i]['C']];
            $data['mz'] = $arr[$i]['D'];
            $data['birth'] = $arr[$i]['E'];
            //$data['idnum'] = $arr[$i]['F'];
            $data['zt'] = $arr[$i]['F'];
            $data['rddate'] = $arr[$i]['G'];
            $data['addr'] = $arr[$i]['H'];
            $data['tel'] = $arr[$i]['I'];
            $data['mgyy'] = $arr[$i]['J'];
            $data['sszb'] = $arr[$i]['K'];
            $data['mgzzsh'] = 1;

            $condition['name'] = $data['name'];
            $condition['tel'] = $data['tel'];

            if($table->where($condition)->find()){
                array_push($sucessArray,$this->databaseSave($table,$data,$condition));
            }else{
                $data['dzzmc'] = trim(str_replace('免过组织生活党员名册','',$arr[1]['A']));
                array_push($sucessArray,$this->databaseAdd($table,$data));
            }
        }
        $result['array'] = $sucessArray;
        $result['result'] = '上传成功';
        return $result;
    }


    /**
     * 保留党籍
     */
    private function parse_bldj($arr, $type){
        $sucessArray = array();

        $sexopt = array('男'=>1,"女"=>2);
        $count = count($arr);
        $table = M('party_member');
        for ($i=3; $i < $count+1; $i++) {
            if ($arr[$i]['A'] == null){
                continue;
            }
            $data['name'] = $arr[$i]['B'];
            $data['sex'] = $sexopt[$arr[$i]['C']];
            $data['mz'] = $arr[$i]['D'];
            $data['birth'] = $arr[$i]['E'];
            //$data['idnum'] = $arr[$i]['F'];
            $data['whcd'] = $arr[$i]['F'];
            $data['dylx'] = $arr[$i]['G'];
            $data['rddate'] = $arr[$i]['H'];
            $data['zt'] = $arr[$i]['I'];
            $data['cgsy'] = $arr[$i]['J'];
            $data['mdd'] = $arr[$i]['K'];
            $data['bldjsj'] = $arr[$i]['L'];
            $data['tel'] = $arr[$i]['M'];
            $data['sszb'] = $arr[$i]['N'];
            $data['bz'] = $arr[$i]['O'];
            $data['cgbldj'] = 1;


            $condition['name'] = $data['name'];
            $condition['tel'] = $data['tel'];
            if($table->where($condition)->find()){
                array_push($sucessArray,$this->databaseSave($table,$data,$condition));
            }else{
                $data['dzzmc'] = trim(str_replace('因私出国（境）保留党籍党员名册','',$arr[1]['A']));
                array_push($sucessArray,$this->databaseAdd($table,$data));
            }
        }
        $result['array'] = $sucessArray;
        $result['result'] = '上传成功';
        return $result;
    }

    /**
     * 双报到党员
     */
    private function parse_sbd($arr, $type){
        $sucessArray = array();

        $sexopt = array('男'=>1,"女"=>2);
        $count = count($arr);
        $table = M('party_member');
        for ($i=3; $i < $count+1; $i++) {
            if ($arr[$i]['A'] == null){
                continue;
            }
            $data['name'] = $arr[$i]['B'];
            $data['sex'] = $sexopt[$arr[$i]['C']];
            $data['mz'] = $arr[$i]['D'];
            $data['birth'] = $arr[$i]['E'];
            //$data['idnum'] = $arr[$i]['F'];
            $data['addr'] = $arr[$i]['F'];
            $data['gzdw'] = $arr[$i]['G'];
            $data['zw'] = $arr[$i]['H'];
            $data['tc'] = $arr[$i]['I'];
            $data['tel'] = $arr[$i]['J'];
            $data["bz"] = $arr[$i]['K'];
            $data['sbd'] = 1;


            $condition['name'] = $data['name'];
            $condition['tel'] = $data['tel'];
            if($table->where($condition)->find()){
                array_push($sucessArray,$this->databaseSave($table,$data,$condition));
            }else{
                $data['dzzmc'] = trim(str_replace('党员“双报到”登记表','',$arr[1]['A']));
                $data['dzzmc'] = trim(str_replace('2017年','',$data['dzzmc']));
                array_push($sucessArray,$this->databaseAdd($table,$data));
            }
        }
        $result['array'] = $sucessArray;
        $result['result'] = '上传成功';
        return $result;
    }


    /**
     * 民主评议
     */
    private function parse_mzpy($arr, $type){
        $sucessArray = array();

        $sexopt = array('男'=>1,"女"=>2);
        $count = count($arr);
        $table = M('data_mzpy');
        $title = $arr[1]["A"] ;

        $year = intval(str_split($title,4)[0]);
        $dzzmc =  str_replace("_","",substr($title,strpos($title,"年")+strlen("年"),strpos($title,"党")-strpos($title,"年")-strlen("年")));

        for ($i=3; $i < $count+1; $i++) {
            if ($arr[$i]['B'] == null){
                continue;
            }
            $data['name'] = $arr[$i]['B'];
            $data['sex'] = $sexopt[$arr[$i]['C']];
            $data['nation'] = $arr[$i]['D'];
            $data['birth'] = $arr[$i]['E'];
            //$data['idnum'] = $arr[$i]['F'];
            $data['state'] = $arr[$i]['F'];
            $data['type'] = $arr[$i]['G'];
            $data['rddate'] = $arr[$i]['H'];
            $data['pydate'] = $arr[$i]['I'];
            $data['suggest'] = $arr[$i]['J'];
            $data["sszb"] = $arr[$i]['K'];

            $data["year"] = $year;
            $data["dzzmc"] = $dzzmc;


//            $condition['name'] = $data['name'];
//            $condition['tel'] = $data['tel'];
//            if($table->where($condition)->find()){
//                array_push($sucessArray,$this->databaseSave($table,$data,$condition));
//            }else{
//                $data['dzzmc'] = trim(str_replace('党员“双报到”登记表','',$arr[1]['A']));
//                $data['dzzmc'] = trim(str_replace('2017年','',$data['dzzmc']));
                array_push($sucessArray,$this->databaseAdd($table,$data));
//            }
        }
        $result['array'] = $sucessArray;
        $result['result'] = '上传成功';
        return $result;
    }

}
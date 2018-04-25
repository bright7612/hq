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
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends Controller
{

    //系统首页
    public function index()
    {
        $this->redirect('Hq/Hq/warningIndex');
        $redirect_url = "/index.php?s=/admin/public/login.html";
        echo "<script language=\"javascript\">";
        echo "document.location=\"" . $redirect_url . "\"";
        echo "</script>";
        exit;
        if (is_login()) {
        }
        hook('homeIndex');
        $default_url = C('DEFUALT_HOME_URL');//获得配置，如果为空则显示聚合，否则跳转
        if ($default_url != '' && strtolower($default_url) != 'home/index/index') {
            redirect(get_nav_url($default_url));
        }
        $this->display();
    }

    public function home()
    {
        if (is_login()) {
        }
        hook('homeIndex');
        $default_url = C('DEFUALT_HOME_URL');//获得配置，如果为空则显示聚合，否则跳转
        if ($default_url != '' && strtolower($default_url) != 'home/index/index') {
            redirect(get_nav_url($default_url));
        }

        $show_blocks = get_kanban_config('BLOCK', 'enable', array(), 'Home');

        $this->assign('showBlocks', $show_blocks);


        $enter = modC('ENTER_URL', '', 'Home');
        $this->assign('enter', get_nav_url($enter));
        $this->display();
    }

    public function pic($id=0)
    {
    	if(!$id)$id ='0';
    	$this->assign('id', $id);
//		dump($id);exit;
		$this->display();
    }
    public function list1($id=0)
    {
    	if(!$id)$id ='0';
    	$this->assign('id', $id);
//		dump($id);exit;
		$this->display();
    }
    protected function _initialize()
    {

        /*读取站点配置*/
        $config = api('Config/lists');
        C($config); //添加配置

        if (!C('WEB_SITE_CLOSE')) {
            $this->error(L('_ERROR_WEBSITE_CLOSED_'));
        }
    }


     public function qrcode($url='',$level=3,$size=20,$type="",$id=''){
     	$url = "http://hqdj.cmlzjz.com/home/ljz/".$type."/id/".$id;
     	if($type=='zymap'){
     		$url = "http://hqdj.cmlzjz.com/home/ljz/zymap/type/".$id;
     	}else if($type == 'dkbm'){
            $url = "http://hqdj.cmlzjz.com/home/ljz/dkyy/id/".$id;
        }
//		$url = 	"http://hqdj.cmlzjz.com/home/ljz/xmbm/id/65";
      Vendor('phpqrcode.phpqrcode');
      $errorCorrectionLevel =intval($level) ;//容错级别
      $matrixPointSize = intval($size);//生成图片大小
    //生成二维码图片
      //echo $_SERVER['REQUEST_URI'];
      $object = new \QRcode();
      $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
     }


}
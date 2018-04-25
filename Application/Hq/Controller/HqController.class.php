<?php
namespace Hq\Controller;

use Think\Controller;

class HqController extends Controller
{
    /**
     * 基层反腐警示案例屏首页
     */
    public function warningIndex()
    {

//        $Model = M('issue');
//        $where['id'] = 47;   //案例列表id
//        $where['status'] = 1;
//        $stitle = $Model->where($where)->field('id,title')->find();
//        $this->assign('stitle',$stitle);
        $this->display('warningIndex');
    }

    public function example()
    {

        $Model = M('issue_content');
        $where['issue_id'] = 60;  //监察制度~上级制度学习
        $where2['issue_id'] = 61;  //监察制度~本镇制度建设
        $where['status'] = 1;
        $where2['status'] = 1;

        $articleList = $Model->where($where)->field('id,title')->order('create_time desc')->select();   //上级制度学习
        $articleList2 = $Model->where($where2)->field('id,title')->order('create_time desc')->select();  //本镇制度建设


        //上海市基层警示案例
        $where1['issue_id'] = 71;
        $where1['status'] = 1;
        $articleList1 = $Model->where($where1)->field('id,title,time,source,content')->order('create_time desc')->select();

        //全国范围内基层警示案例
        $where3['issue_id'] = 72;
        $where3['status'] = 1;
        $articleList3 = $Model->where($where3)->field('id,title,time,source,content')->order('create_time desc')->select();

        foreach ($articleList1 as $k=>&$item){
            $item['content'] = strip_tags($item['content'],"");  //过滤标签
        }

        foreach ($articleList3 as $k=>&$item){
            $item['content'] = strip_tags($item['content'],"");  //过滤标签
        }


        //廉政教育片
        $video = $Model->query("SELECT
                                        CONCAT(
                                            file.savepath,
                                            file.savename
                                        ) AS path,
                                        content.cover_id,
                                        content.id,
                                        content.title
                                  
                                    FROM
                                        ljz_issue_content AS content
                                    JOIN ljz_file AS file ON content.video_id = file.id
                                    WHERE
                                        content.`status` = 1
                                    AND content.issue_id = 69
                                    AND video_status = 1                       
                                    ORDER BY content.create_time
                                     LIMIT 0,1");

        foreach ($video as $k=>&$v){
            $items = get_cover($v['cover_id']);
            $v['img_url'] = $items['path'];
        }

        //廉政小贴士'
        $where['status'] = 1;
        $where['issue_id'] = 70;
        $lianzheng = $Model->where($where)->field('id,title,content')->order('create_time DESC')->limit(5)->select();

        $this->assign('articleList',$articleList);
        $this->assign('articleList1',$articleList1);
        $this->assign('articleList2',$articleList2);
        $this->assign('articleList3',$articleList3);
        $this->assign('video',$video['0']);
        $this->assign('lianzheng',$lianzheng);
        $this->display('example');
    }

    /**
     * 视频列表
     */
    public function warningList()
    {
        $Model = M('issue_content');
        $where['status'] = 1;
        $where['issue_id'] = 69;
        $videoList = $Model->where($where)->field('id,title')->order('create_time DESC')->select();

        $this->assign('videoList',$videoList);
        $this->display('warningList');
    }

    /**
     * 详情页视频播放
     */
    public function warningVideo()
    {
        $Model = M('issue_content');
        $id = I('id');

        $video = $Model->query("SELECT
                                        CONCAT(
                                            file.savepath,
                                            file.savename
                                        ) AS path,
                                        content.cover_id,
                                        content.id,
                                        content.title
                                  
                                    FROM
                                        ljz_issue_content AS content
                                    JOIN ljz_file AS file ON content.video_id = file.id
                                    WHERE
                                        content.`status` = 1
                                    AND content.issue_id = 69
                                    AND content.id = $id                                     
                                    ORDER BY content.create_time
                                ");


        $this->assign('video',$video['0']);
        $this->display('warningVideo');

    }

    public function articeDetail()
    {
            $Model = M('issue_content');
            $id = I('id');
            $where['id'] = $id;
            $where['status'] = 1;
            $detail = $Model->where($where)->field('title,content,source,time')->find();
            $this->assign('detail',$detail);
            $this->display('case_detail');
    }

    public function organize_Detail()
    {
        $Model = M('issue_content');
        $id = I('id');
        $where['id'] = $id;
        $where['status'] = 1;
        $detail = $Model->where($where)->field('title,content,source,time')->find();
        $this->assign('detail',$detail);
        $this->display('organize_detail');
    }

    /**
     * 美丽合庆大屏幕显示
     */
    public function djIndex()
    {


        /**
         * 美丽合庆概况
         */
        $content = M('issue_content');
        $where['issue_id'] = 5;
        $where['status'] = 1;
        $activity = $content->where($where)->field('id,title,cover_id')->order('create_time desc')->limit(5)->select();

        /**
         * 合庆之路专题
         */
        $video = $content->query("SELECT
                                        CONCAT(
                                            file.savepath,
                                            file.savename
                                        ) AS path,
                                        content.cover_id,
                                        content.id,
                                        content.title
                                  
                                    FROM
                                        ljz_issue_content AS content
                                    JOIN ljz_file AS file ON content.video_id = file.id
                                    WHERE
                                        content.`status` = 1
                                    AND content.issue_id = 6 
                                    AND video_status = 1
                                    AND  video_type = 2
                                    ORDER BY content.create_time
                                     LIMIT 0,1");

        foreach ($video as $k=>&$v){
            $items = get_cover($v['cover_id']);
            $v['img_url'] = $items['path'];
        }


        $this->assign('video',$video['0']);

        /**
         * 党建每月活动中心
         */
//        $where2['issue_id'] = 54;
//        $where2['status'] = 1;
//        $month = $content->where($where2)->field('id,title')->order('create_time desc')->limit(6)->select();

        /**
         * 美丽合庆二级栏目
         */
        $Model = M('issue');
        $where1['pid'] = 4;
        $where1['status'] = 1;
        $stitle = $Model->where($where1)->field('id,title')->select();
        /**
         * 党建每月活动 活动照片 id
         */
        $where3['pid'] = 50;
        $where3['status'] = 1;
        $dj_id = $Model->where($where3)->field('id,title')->select();

        /**
         * 党建活动
         */
        $where4['issue_id'] = 54;
        $where4['status'] = 1;
        $photo = $content->where($where4)->field('id,title,content,cover_id')->order('create_time desc')->limit(3)->select();
        $photo_title = $content->where($where4)->field('id,title,content,cover_id')->order('create_time desc')->limit(4)->select();

        /**
         * 中心每月活动预告
         */
        $where5['issue_id'] = 55;
        $where5['status'] = 1;
        $message = $content->where($where5)->field('id,title,content')->order('create_time desc')->limit(5)->select();


        foreach ($message as $k=>&$value){
            $value['content'] = strip_tags($value['content'],"");  //过滤标签
        }


        foreach ($photo as $k=>&$v){
            $items = get_cover($v['cover_id']);   // 用get_cover()函数将解析图片id获取图片的信息，包含图片的路径
            $v['content'] = strip_tags($v['content'],"");
            $v['cover_id'] = $items['path'];
        }

//        foreach ($activity as $k=>&$v){
//            $items = get_cover($v['cover_id']);   // 用get_cover()函数将解析图片id获取图片的信息，包含图片的路径
//            $v['cover_id'] = $items['path'];
//        }


        foreach ($activity as $k=>&$item){
            //判断是否有多个图片
            if(strpos($item['cover_id'],',')){
                $img_id = explode(',',$item['cover_id']);

                foreach ($img_id as $k=>&$v){
                    $items = get_cover($v);
                    $imgs[$k]['cover_id'] = $items['path'];
                    $imgs[$k]['id'] = $item['id'];
                    $imgs[$k]['title'] = $item['title'];
                }
            }else{
                //单个图片
//              $path = M('picture')->where(array('id'=>$item['cover_id']))->field('path')->find();
                $items = get_cover($item['cover_id']);
                $imgss[$k]['cover_id'] = $items['path'];
                $imgss[$k]['id'] = $item['id'];
                $imgss[$k]['title'] = $item['title'];
            }

        }


        if($imgss && $imgs){
            $images = array_merge($imgss,$imgs);
        }elseif ($imgss){
            $images = $imgss;
        }elseif ($imgs){
            $images = $imgs;
        }


        $this->assign('message',$message);
        $this->assign('photo',$photo);
        $this->assign('dj',$dj_id);
        $this->assign('photo_title',$photo_title);
        $this->assign('stitle',$stitle);
        $this->assign('activity',$images);
        $this->display('daping2');
    }

    /**
     * 场地预约列表
     */
    public function bespoke()
    {
        $Model = M('data_hdyg');
        $where['status'] = 1;
        $where['type'] = 6;
        $siteList = $Model->where($where)->field('id,name,content,addr,count,zb')->select();

        foreach ($siteList as $k=>&$v){
           $v['qrcode'] ='http://hqdj.cmlzjz.com/hq/hq/qrcode/id/'.$v['id'].'?type=1';
        }

//        dump($siteList);
        $this->assign('siteList',$siteList);
//        dump($siteList);
        $this->display('siteList');
    }

    public function demand()
    {

        $this->display('demand1');
    }

    /**
     * 党员心声
     */
    public function dyxs()
    {

        $this->display('dyxs');
    }

    /**
     * 组织构架子页面
     */
    public function organize()
    {
        $this->display('jiagou');
    }

    public function organizeList()
    {
        $Model = M('dzz');
        $id = I('id');
        if($id == 1){
            $where['type'] = '村、居民区党组织';
            $title = '村、居民区党组织';
        }elseif ($id == 2){
            $where['type'] = '机关事业单位党组织';
            $title = '机关事业单位党组织';
        }elseif ($id == 3){
            $where['type'] = '综合工作党委';
            $title = '综合工作党委';
        }elseif ($id == 4){

            $where['type'] = '其他';
            $title = '其他';
//            $res = $Model->where($sql)->field("name as title,id,content,'$title' as stitle")->select();
//            $count = count($res);
//            if($count == 1){
//                $this->assign('detail',$res['0']);
//                $this->display('detail');
//            }else{
//                $this->assign('result',$res);
//                $this->display('organizeList');
//            }
//
//            exit();
        }

        $where['status'] = 1;
        $res = $Model->where($where)->field("name as title,id,content,'$title' as stitle")->select();

        $this->assign('result',$res);
        $this->display('organizeList');


    }

    public function organizeDetail()
    {
        $Model = M('dzz');
        $where['id'] = I('id');
        $where['status'] = 1;
        $res = $Model->where($where)->field('name as title,content')->find();
        if($res){
            echo json_encode(array('status'=>0,'msg'=>'请求成功','data'=>$res));
        }else{
            echo json_encode(array('status'=>-1,'msg'=>'请求失败','data'=>$res));
        }

    }


    /**
     *区域化党建
     */

    public function localDj()
    {
        $Model = M('issue');
        $where['status'] = 1;
        $where['id'] = 51;
        $stitle_id = $Model->where($where)->field('id,title')->find();
        $this->assign('stitle',$stitle_id);
        $this->display('quyudangjian');
    }

    /**
     * 文章列表
     */
    public function articleList($type = '')
    {
        $Model = M('issue_content');
        $where['issue_id'] = I('id');

        $where['ljz_issue_content.status'] = 1;
        $result = $Model ->join('ljz_issue as issue on ljz_issue_content.issue_id  = issue.id')
                         ->field('issue.title as stitle,ljz_issue_content.id,ljz_issue_content.content,ljz_issue_content.title,ljz_issue_content.cover_id')
                         ->where($where)
                         ->order('ljz_issue_content.create_time desc')
                         ->select();

        foreach ($result as $k=>&$v){
            $items = get_cover($v['cover_id']);   // 用get_cover()函数将解析图片id获取图片的信息，包含图片的路径
            $v['cover_id'] = $items['path'];

        }

        $count = count($result);

        /**
         * type等于1则显示微心愿
         */
        if($type == 1){
            foreach ($result as $k=>&$v) {
                $v['content'] = strip_tags($v['content']);
            }
            $this->assign('detail',$result);
            $this->display('heart');
            exit();
        }

        if($count == 1){
            $this->assign('detail',$result['0']);
            $this->display('detail');
        }else{
            $this->assign('result',$result);
            $this->display('list');
        }

    }

    public function djActivityList()
    {
        $Model = M('issue_content');
        $where['issue_id'] = I('id');


        $where['ljz_issue_content.status'] = 1;
        $result = $Model ->join('ljz_issue as issue on ljz_issue_content.issue_id  = issue.id')
            ->field('issue.title as stitle,ljz_issue_content.id,ljz_issue_content.content,ljz_issue_content.title')
            ->where($where)
            -> order('ljz_issue_content.create_time desc')
            ->select();

        $count = count($result);
        if($count == 1){
            $this->assign('detail',$result['0']);
            $this->display('detail');
        }else{
            $this->assign('result',$result);
            $this->display('djactivity');
        }
    }



    public function  videoList()
    {
        $Model = M();
        $id = I('id');
        $res = $Model->query("SELECT
                                    CONCAT(
                                        file.savepath,
                                        file.savename
                                    ) AS path,
                                    content.id,
                                    content.title
                                FROM
                                    ljz_issue_content AS content
                                JOIN ljz_file AS file ON content.video_id = file.id
                                WHERE
                                    `status` = 1
                                AND issue_id = $id                           
                                ORDER BY
                                    content.create_time DESC
                                    ");


        $count = count($res);

        if($count == 1){
            $this->assign('video',$res['0']);
            $this->display('videoplay');
        }else{
            $this->assign('result',$res);
            $this->display("videoList");
        }

    }
    public function  brand_videoList()
    {
        $Model = M();
        $id = I('id');
        $res = $Model->query("SELECT
                                    CONCAT(
                                        file.savepath,
                                        file.savename
                                    ) AS path,
                                    content.id,
                                    content.title
                                FROM
                                    ljz_issue_content AS content
                                JOIN ljz_file AS file ON content.video_id = file.id
                                WHERE
                                    `status` = 1
                                AND issue_id = $id                           
                                ORDER BY
                                    content.create_time DESC
                                    ");


        $count = count($res);

        if($count == 1){
            $this->assign('video',$res['0']);
            $this->display('brand_video');
        }else{
            $this->assign('result',$res);
            $this->display("brand_videoList");
        }

    }

    public function video()
    {
        $id = I('id');
        $Model = M();
        $video = $Model->query("SELECT
                                        CONCAT(
                                            file.savepath,
                                            file.savename
                                        ) AS path,
                                        content.id,
                                        content.title
                                    FROM
                                        ljz_issue_content AS content
                                    JOIN ljz_file AS file ON content.video_id = file.id
                                    WHERE
                                        `status` = 1
                                    AND content.id = $id
                                    ORDER BY
                                        content.create_time DESC
                                        ");


        $this->assign('video',$video['0']);
        $this->display('videoplay');
    }
    public function brand_video()
    {
        $id = I('id');
        $Model = M();
        $video = $Model->query("SELECT
                                        CONCAT(
                                            file.savepath,
                                            file.savename
                                        ) AS path,
                                        content.id,
                                        content.title
                                    FROM
                                        ljz_issue_content AS content
                                    JOIN ljz_file AS file ON content.video_id = file.id
                                    WHERE
                                        `status` = 1
                                    AND content.id = $id
                                    ORDER BY
                                        content.create_time DESC
                                        ");


        $this->assign('video',$video['0']);
        $this->display('brand_video');
    }

    public function detail()
    {
        $Model = M('issue_content');
        $where['id'] = I('id');
        $where['status'] = 1;
        $detail = $Model->where($where)->field('title,content')->find();

        $this->assign('detail',$detail);
        $this->display('detail');
    }

    public function general()
    {
        $Model = M('issue');
        $content = M('issue_content');

        $where['pid'] = 7;
        $where['status'] = 1;
        $stitle = $Model->where($where)->field('id,title')->select();

        $where2['pid'] = 4;
        $where2['status'] = 1;
        $video_id = $Model->where($where2)->field('id,title')->select();

        //概貌概况
        $where1['status'] = 1;
        $where1['issue_id'] = 5;
        $general = $content ->where($where1)->field('id,content,issue_id')->order('create_time desc')->find();
//        $general['content'] = strip_tags($general['content'],"");  //过滤标签

        $video = $content->query("SELECT
                                        CONCAT(
                                            file.savepath,
                                            file.savename
                                        ) AS path,
                                        content.cover_id,
                                        content.id,
                                        content.title
                                    FROM
                                        ljz_issue_content AS content
                                    JOIN ljz_file AS file ON content.video_id = file.id
                                    WHERE
                                        content.`status` = 1
                                    AND content.issue_id = 6 
                                    AND video_status = 1
                                    AND  video_type = 1
                                    ORDER BY content.create_time
                                     LIMIT 0,1");

        $this->assign('video',$video['0']);
        $this->assign('video_id',$video_id);
        $this->assign('general',$general);
        $this->assign('stitle',$stitle);
        $this->display('gaikuang');
    }

    /**
     *书记带教
     */
    public function shuji()
    {
        $Model = M('data_js');
        $where['status'] = 1;
        $where['mtype'] = 2;

        $shuji = $Model->where($where)->field('id,name,`desc` as content,pic')->select();
        foreach ($shuji as $k=>&$v){
            $items = get_cover($v['pic']);   //获取图片路径
            $v['pic'] = $items['path'];
            $v['content'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",strip_tags($v['content'],""));  //过滤标签和空格
        }

        $this->assign('shuji',$shuji);
        $this->display('shujiList');
    }

    /**
     * 书记带教详情页
     */

    public function shujiDetail()
    {
        $Model = M('data_js');
        $id = I('id');
        $where['id'] = $id;
        $where['status'] = 1;
        $detail = $Model->where($where)->field('id,`name` as title,`desc` as content,pic')->find();
        $items = get_cover($detail['pic']);   //获取图片路径
        $detail['pic'] = $items['path'];

        $this->assign('detail',$detail);
        $this->display('person');
    }

    /**
     * 合庆党建
     */
    public function party()
    {

        //活动概况
        $content = M('issue_content');
        $where['issue_id'] = 51;
        $where['status'] = 1;
        $activity = $content->where($where)->field('id,title,cover_id')->order('create_time desc')->select();

        //组织管理
        $where1['issue_id'] = 52;
        $where1['status'] = 1;
        $rule = $content->where($where1)->field('id,title')->order('create_time desc')->limit(5)->select();

        //党员先锋
        $where5['issue_id']= 53;
        $where5['status']= 1;
        $organize = $content->where($where5)->field('id,title')->order('create_time desc')->Limit(5)->select();

        //活动概况 制度建设  组织管理的id
        $Model = M('issue');
        $where3['pid'] = 50;
        $where3['status'] = 1;
        $stitle_id = $Model->where($where3)->field('id,title')->select();

        //书记带教
        $Model = M('data_js');
        $where4['status'] = 1;
        $where4['mtype'] = 2;
        $shuji = $Model->where($where4)->field('id,pic,desc')->order('id desc')->find();
        $items = get_cover($shuji['pic']);   //获取图片路径
        $shuji['pic'] = $items['path'];
        $shuji['desc'] = strip_tags($shuji['desc'],"");  //过滤标签

        foreach ($activity as $k=>&$v){
            $items = get_cover($v['cover_id']);   // 用get_cover()函数将解析图片id获取图片的信息，包含图片的路径
            $v['cover_id'] = $items['path'];
        }

        $this->assign('shuji',$shuji);
        $this->assign('stitle',$stitle_id);
        $this->assign('rule',$rule);
        $this->assign('organize',$organize);
        $this->assign('activity',$activity);
        $this->display('dangjian');
    }


    /**
     * 社区党校
     */
    public function communitySchool()
    {
        $this->display('dangxiao');
    }

    /**
     * 课程列表
     */

    public function lessionList()
    {
        $Model = M('apply_djkc');
        $where['status'] = 1;
        $less = $Model->where($where)->field("id,course_name,`datestr` AS time,content,addr,jbdw as organize,count")->order('id desc')->select();

        foreach ($less as $k=>$v){
            $less[$k]['qrcode'] ='http://hqdj.cmlzjz.com/hq/hq/qrcode/id/'.$v['id'].'?type=4';
        }


        foreach ($less as $k=>&$v){
            $count = M('data_dkbm')->where('aid=' . $v['id'])->sum("num");
            $v['count'] = ($v['count'] - $count) . '/' . $v['count'];
        }


        $this->assign('less',$less);
        $this->display('lesson');
    }

    /**
     * @param string $url
     * @param int $level
     * @param int $size
     * @param string $type
     * @param string $id
     * 生成二维码
     */
    public function qrcode($url='',$level=3,$size=20,$type="",$id=''){
        if($type == 1){
            $url = "http://hqdj.cmlzjz.com/home/ljz/hdxq/id/".$id;
        } else if ($type == 2){
            $url = "http://hqdj.cmlzjz.com/home/ljz/hdbm/id/".$id;
        }else if($type == 3){
            $url = "http://hqdj.cmlzjz.com/home/ljz/gycDetail/id/".$id;
        } else if($type == 4){
            $url = "http://hqdj.cmlzjz.com/home/ljz/dkyy/id/".$id;
        }

//		$url = 	"http://hqdj.cmlzjz.com/home/ljz/xmbm/id/65";
        Vendor('phpqrcode.phpqrcode');
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        //生成二维码图片
//        return $_SERVER['REQUEST_URI'];
        $object = new \QRcode();
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }

    /**
     * 讲师风采
     */

    public function teacherList()
    {
        $Model = M('data_js');
        $where['mtype'] = 1;
        $where['status'] = 1;
        $teacher = $Model->where($where)->field('id,name as title,pic,`desc` as content')->select();

        foreach ($teacher as $k=>&$v){
            $items = get_cover($v['pic']);   // 用get_cover()函数将解析图片id获取图片的信息，包含图片的路径
            $v['pic'] = $items['path'];
        }


        $this->assign('result',$teacher);
        $this->display('teacherList');
    }

    public function teacherDetail()
    {
        $Model = M('data_js');
        $where['id'] = I('id');
        $teacher = $Model->where($where)->field('id,name as title,`desc` as content')->find();
        
        $this->assign('detail',$teacher);
        $this->display('detail');
    }




    /*
     * 红色生活
     */
    public function redLife()
    {
        $Model = M();
        $id = I('id');
//        $res = $Model->query("");
        $this->display('hongse');
    }

    /**
     * 组织管理转接详情
     */

    public function dwDetail()
    {
        $content = M('issue_content');
        $where['issue_id'] = 58;
        $where['status'] = 1;
        $detail = $content->where($where)->field('id,title,content')->find();

        $this->assign('detail',$detail);
        $this->display('dwDetail');
    }

    public function information()
    {
        /**
         * 党建每月活动中心
         */
        $content = M('issue_content');
        $where2['issue_id'] = 54;
        $where2['status'] = 1;
        $month = $content->where($where2)->field('id,title')->order('create_time desc')->limit(5)->select();

        /**
         * 活动照片
         */
        $where4['issue_id'] = 57;
        $where4['status'] = 1;
        $photo = $content->where($where4)->field('id,content,cover_id,title')->order('create_time desc')->limit(5)->select();

        /**
         *  二级栏目id
         */
        $Model = M('issue');
        $where3['id'] = array('in','54,57');
        $where3['status'] = 1;
        $stitle_id = $Model->where($where3)->field('id,title')->select();

        /**
         * 党建服务站每月活动
         */
        $server = M('data_hdyg');
        $where['status'] = 1;
        $where['type'] = 8;
        $activity = $server->where($where)->field('id,name')->order('date desc')->limit(4)->select();

        foreach ($photo as $k=>&$v){
            $items = get_cover($v['cover_id']);   // 用get_cover()函数将解析图片id获取图片的信息，包含图片的路径
            $v['cover_id'] = $items['path'];
        }

        /**
         * 党课列表
         */
        $class = M('apply_djkc');
        $where1['status'] = 1;
        $classList = $class->where($where1)->field('id,course_name as title')->limit(4)->select();

        $this->assign('class',$classList);
        $this->assign('activity',$activity);
        $this->assign('stitle',$stitle_id);
        $this->assign('photo',$photo);
        $this->assign('month',$month);
        $this->display('xinxi');
    }


    /**
     * 党建地图
     */
    public function map()
    {
        if(IS_POST){
            $Model = M('data_zyqd');
            $where['status']= 1;
            $where['type']= I('category_id');
            $where['lat'] != '';
            $maplist = $Model->where($where)->field('id,name as title,addr as address,lat,lng')->select();

            if($maplist){
                echo json_encode(array('status'=>1,'msg'=>'请求成功!','result'=>$maplist));
            }else{
                echo json_encode(array('status'=>1,'msg'=>'请求成功,暂无数据!!!','result'=>$maplist));
            }

        }else{
            echo json_encode(array('status'=>-1,'msg'=>'请求失败'));
        }

    }

    public function mapList()
    {

        $this->display('map');
    }



    /**
     * 党建服务站每月活动详情
     */
    public function activityDetail()
    {
        $Model = M('data_hdyg');
        $id = I('id');
        $where['id'] = $id;
        $where['status'] = 1;
        $detail = $Model->where($where)->field('name as title,content')->find();

        $this->assign('detail',$detail);
        $this->display('detail');
    }

    /**
     * 党建服务站每月活动列表
     */
    public function activityList()
    {
        $Model = M('data_hdyg');
        $where['status'] = 1;
        $where['type'] = 1;
        $res = $Model->where($where)->field('name as title,id')->select();


        $this->assign('result',$res);
        $this->display('list');
    }

    /**
     * 党课列表
     */
    public function classList()
    {
        $class = M('apply_djkc');
        $where1['status'] = 1;
        $classList = $class->where($where1)->field('id,course_name as title')->select();

        $this->assign('result',$classList);
        $this->display('classList');
    }

    /**
     * 党课列表详情
     */

    public function classDetail()
    {
        $class = M('apply_djkc');
        $id = I('id');
        $where1['status'] = 1;
        $where1['id'] = $id;
        $classList = $class->where($where1)->field('id,course_name as title,content')->find();

        $this->assign('detail',$classList);
        $this->display('detail');
    }

    /**
     * 合心圆
     */
    public function hexinyuan()
    {
        $Model = M('data_hdyg');
        $where['status'] = 1;
        $where['type'] = 10;
        $res = $Model->where($where)->field("id,name,content,count,addr,zb,FROM_UNIXTIME(date,'%Y-%m-%d') as time")->select();
        foreach ($res as $k=>&$v){
           $v['qrcode'] ='http://hqdj.cmlzjz.com/hq/hq/qrcode/id/'.$v['id'].'?type=2';
        }
        $this->assign('res',$res);
        $this->display('hexinyuanList');
    }

    /**
     * 项目直通车
     */
    public function projectList()
    {
        $Model = M('project_claim');
        $where['status'] = 1;
        $project = $Model->where($where)->field("id,name,`desc`")->select();
        foreach ($project as $k=>&$v){
            $v['qrcode'] = 'http://hqdj.cmlzjz.com/hq/hq/qrcode/id/'.$v['id'].'?type=3';
        }
        $this->assign('project',$project);
        $this->display('projectList');
    }


//    public function twoList()
//    {
//        $Model = M();
//        $id = I('id');
//        $res = $Model->query("");
//        $this->display();
//    }

    public function wxCode()
    {
        $id = I('id');

        //讲师风采
        if($id == 1){
            $title = '讲师风采';
            $img = 1;
        }

        //党建地图
        if ($id == 2){
            $title = '党建地图';
            $img = 2;
        }

        //场地预约
        if ($id == 3){
            $title = '场地预约';
            $img = 3;
            $content = "“场地预约”为您提供预约使用党建中心、支部、村居等公共场地、设备的桥梁，提高了公共资源的利用效率。扫描左侧的二维码试一试吧。";
        }

        //微心愿
        if ($id == 4){
            $title = '微心愿';
            $img = 4;
            $content = "在“微心愿”平台提出自己的小心愿，随着心愿的发布和认领，许愿人和认领人之间的心走近了，爱心的“接力棒”也在继续传递着。扫描左侧的二维码一起参加吧。";
        }

        //需求传声筒
        if ($id == 5){
            $title = '需求传声筒';
            $img = 5;
            $content = "“当好党音传声筒 做好群众贴心人”合庆党建平台提供“需求传声筒”功能，群众可以随时把自己的需求提交到党建中心，让党时时刻刻听到群众的声音。扫描左侧的二维码试一试吧。";
        }

        //项目直通车
        if ($id == 7){
            $title = '项目直通车';
            $img = 7;
            $content = "合庆镇党建注重打造具有合庆特色的党组织服务群众机制,探索建立了系统化的“项目直通车”党建服务体系,为民服务水平不断提升。开通党员志愿服务“直通车”,以村、居委为站点,定期停靠,集中为群众提供维修、理发、修脚、理疗、清扫等方面的免费服务。创建“互联网+党建”工作“直通车”,利用信息技术让居民在家中即可享受到社区全方位的服务,打造热点难点问题专业“直通车”。扫描左侧的二维码一起参加吧。";
        }

        //资源清单
        if ($id == 6){
            $title = '资源清单';
            $img = 6;
            $content = "“资源清单”整合了合庆镇的活动场地、社会组织、驻区单位合作共建单位等，旨在通过党建引领，汇聚辖区内企事业资源，实现互融互通，协同发展。扫描左侧的二维码看看吧。";
        }

        //党费核定
        if ($id == 8){
            $title = '党费核定';
            $img = 8;
            $content = "党费是党员向党组织交纳的用于党的事业和党的活动的经费。 交纳党费是党员对党组织应尽的义务，是对党员党性的检验，也是党员关心党的事 业的一种表现。按照《党章》规定，党员向党组织交纳党费，是党员必须具备的起码条件。";
        }

        //组织关系接转
        if($id == 9){
            $title = '组织关系接转';
            $img = 9;
        }

        //共建单位
        if($id == 10){
            $title = '共建单位';
            $img = 10;
            $content = "按照“组织建设实现联建、党建活动实现联动、党员教育实现联管、文明建设实现联创、民生服务实现联帮、文体活动实现联谊、公益事业实现联办”的原则，合庆镇区域党建产生了很多共建单位，查看共建单位信息请扫描二维码" ;
        }

        //合心圆
        if($id == 11){
            $title = '合心圆';
            $img = 11;
            $content = "“合心圆”是合庆镇党建的品牌，是具有合庆特色的党建创新探索，旨在通过党建引领，促进辖区内企事业单位互融互通，协同发展。自创建以来，携手辖区内各单位，开展了一系列服务发展、惠及民生的实体化运作项目，内容覆盖党建共建、医疗服务、教育共享、社会治理、金融创新、军地协作、项目建设等各领域，构筑起信息互通、资源共享、和谐共建、互惠共赢的基层“大党建”格局。扫描左侧的二维码一起参加吧。";
        }

        //党员心声和微心愿一样
        if($id == 12){
            $title = '党员心声';
            $img = 4;
            $content = "在“党员心声”平台提出自己的小心声，随着心声的发布和认领，许愿人和认领人之间的心走近了，爱心的“接力棒”也在继续传递着。扫描左侧的二维码一起参加吧。";
        }

        $this->assign('title',$title);
        $this->assign('content',$content);
        $this->assign('img',$img);
        $this->display('code');
    }


    /**
     * 合庆党建品牌项目首页
     *
     */

    public function brandIndex()
    {
        $Model = M('issue_content');
        $where['issue_id'] = 17;
        $where['status'] = 1;
        $bg_detail = $Model->where($where)->field('id,content,cover_id')->order('create_time desc')->limit(1)->select();

        $where['issue_id'] = 28;
        $where['status'] = 1;
        $bg_detail1 = $Model->where($where)->field('id,content,cover_id')->order('create_time desc')->limit(1)->select();

        $where['issue_id'] = 39;
        $where['status'] = 1;
        $bg_detail2 = $Model->where($where)->field('id,content,cover_id')->order('create_time desc')->limit(1)->select();

        $Model = M();
        $video = $Model->query("SELECT
                                        CONCAT(
                                            file.savepath,
                                            file.savename
                                        ) AS path,
                                        content.id,
                                        content.title
                                    FROM
                                        ljz_issue_content AS content
                                    JOIN ljz_file AS file ON content.video_id = file.id
                                    WHERE
                                        `status` = 1
                                    AND content.issue_id = 26
                                    ORDER BY
                                        content.create_time DESC
                                    LIMIT 0,1
                                        ");

        foreach ($bg_detail as $k=>&$item){
            $item['content'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",strip_tags($item['content'],""));  //过滤标签和空格

            $items = get_cover($item['cover_id']);   // 用get_cover()函数将解析图片id获取图片的信息，包含图片的路径
            $item['cover_id'] = $items['path'];
        }

        foreach ($bg_detail1 as $k=>&$item){
            $item['content'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",strip_tags($item['content'],""));  //过滤标签和空格
            $items = get_cover($item['cover_id']);   // 用get_cover()函数将解析图片id获取图片的信息，包含图片的路径
            $item['cover_id'] = $items['path'];
        }

        foreach ($bg_detail2 as $k=>&$item){
            $item['content'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/","",strip_tags($item['content'],""));  //过滤标签和空格
            $items = get_cover($item['cover_id']);   // 用get_cover()函数将解析图片id获取图片的信息，包含图片的路径
            $item['cover_id'] = $items['path'];
        }


        $this->assign('bg_detail',$bg_detail['0']);
        $this->assign('bg_detail1',$bg_detail1['0']);
        $this->assign('bg_detail2',$bg_detail2['0']);
        $this->assign('video',$video['0']);
        $this->display('index');
    }

    /**
     * 二级栏目
     */
    public function categoryList()
    {
        $Model = M('issue');
        $id = I('id');
        $where['pid'] = $id;
        $where['status'] = 1;
        $stitle = $Model->where($where)->field('id,title')->select();

//        dump($stitle);
        $this->assign('stitle',$stitle);

        if($id == 16){
            $this->display('cunmin');
        }

        if($id == 27){
            $this->display('djContent');
        }

        if($id == 38){
            $this->display('sanshu');
        }

    }

    /**
     * 工作机制
     */
    public function mechanism()
    {
        $Model = M('issue');
        $where['id'] = array('in','18,19,20');
        $where['status'] = 1;
        $stitle = $Model->where($where)->field('id,title')->select();

        $this->assign('stitle',$stitle);
        $this->display('jizhi');
    }

    /**
     * 内容机制二级栏目
     */

    public function contentList()
    {
        $Model = M('issue');
        $where['pid'] = 27;
        $where['status'] = 1;
        $stitle = $Model->where($where)->field('id,title')->select();

        $this->assign('stitle',$stitle);
        $this->display('neirong');
    }

    /**
     * 品牌文章列表
     */
    public function brandList()
    {
        $Model = M('issue_content');
        $where['issue_id'] = I('id');
        $where['ljz_issue_content.status'] = 1;
        $result = $Model ->join('ljz_issue as issue on ljz_issue_content.issue_id  = issue.id')
            ->field('issue.title as stitle,ljz_issue_content.id,ljz_issue_content.content,ljz_issue_content.title')
            ->where($where)
            ->order('ljz_issue_content.create_time desc')
            ->select();

//        $aa = M()->getLastsql();
//        dump($aa);die;

        $count = count($result);
        if($count == 1){
            $this->assign('detail',$result['0']);
            $this->display('brandDetail');
        }else{
            $this->assign('result',$result);
            $this->display('brandList');
        }

    }

    public function brandDetail()
    {
        $Model = M('issue_content');
        $where['id'] = I('id');
        $where['status'] = 1;
        $detail = $Model->where($where)->field('title,content')->find();

        $this->assign('detail',$detail);
        $this->display('detail');
    }

}
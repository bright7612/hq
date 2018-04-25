<?php
namespace Admin\Controller;

use Think\Controller;

;
// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:POST');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');
class XchkController extends Controller
{
    public function uploadImg(){
        $Picture = D('Admin/Picture');
        $driver = modC('PICTURE_UPLOAD_DRIVER','local','config');
        $driver = check_driver_is_exist($driver);
        $uploadConfig = get_upload_config($driver);
        $info = $Picture->upload(
            $_FILES,
            C('PICTURE_UPLOAD'),
            $driver,
            $uploadConfig
        );
        //TODO:上传到远程服务器
        /* 记录图片信息 */
        if ($info) {
            $return['status'] = 1;
            if ($info['Filedata']) {
                $return = array_merge($info['Filedata'], $return);
            }
            if ($info['download']) {
                $return = array_merge($info['download'], $return);
            }
            /*适用于自动表单的图片上传方式*/
            if ($info['file'] || $info['files']) {
                $return['data']['file'] = $info['file']?$info['file']:$info['files'];
            }
            /*适用于自动表单的图片上传方式end*/
            $aWidth= I('get.width',0,'intval');
            $aHeight=   I('get.height',0,'intval');
            if($aHeight<=0){
                $aHeight='auto';
            }
            if($aWidth>0){
                $return['path_self']=getThumbImageById($return['id'],$aWidth,$aHeight);
            }
        } else {
            $return['status'] = 0;
            $return['info'] = $Picture->getError();
        }
        /* 返回JSON数据 */
        $this->ajaxReturn($return);
    }


    /**
     * 保存提交信息
     */
    public function yearAdd()
    {
        $Model = M('year');
        $data['name'] = I('name');
        $data['text'] = I('text');
        $data['photoid'] = I('photoId');
//        $data['photoUrl'] = I('photoUrl');
        $data['uid'] = base64_encode('uid'.time());

//      echo json_encode($data);

        if(!empty($data)){
            $res = $Model->add($data);
            if($res){
                echo json_encode(array('status'=>0,'msg'=>'上传成功','uid'=>$data['uid']));
            }else{
                echo json_encode(array('status'=>-1,'msg'=>'上传失败'));
            }
        }else{
            echo json_encode(array('status'=>-2,'msg'=>'上传数据为空'));
        }

    }

    /**
     * 返回
     */
    public function yearDetail()
    {
        $Model = M('year');
        $uid = I('uid');

        if ($uid){
            $res = $Model->where(array('uid'=>$uid))->find();
            $items = get_cover($res['photoid']);
            $res['photoPath'] = 'http://hqdj.cmlzjz.com'.$items['path'];


            if($res){
                echo json_encode(array('status'=>0,'msg'=>'查询成功','data'=>$res));
            }else{
                echo json_encode(array('status'=>-1,'msg'=>'查询失败','data'=>$res));
            }
        }


    }



}
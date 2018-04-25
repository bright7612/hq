<?php
namespace Api\Controller;

use Think\Controller;

;
// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:POST');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');
class UploadController extends Controller
{
    public function uploadImg(){
        dump(1);exit;
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

}
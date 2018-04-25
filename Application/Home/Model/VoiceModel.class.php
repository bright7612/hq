<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Model;

use Think\Model;

/**
 * 文件模型
 * 负责文件的下载和上传
 */
class VoiceModel extends Model
{

    protected $_validate = array(
        array("voice", "10,200", "心声内容必须在10-200个字范围内", self::MUST_VALIDATE, "length"),
        array("name", "require", "姓名不能为空", self::MUST_VALIDATE),
        array("name", "1,20", "姓名长度不能超过20", self::MUST_VALIDATE, "length")
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
    );

    function addOrUpdate($data)
    {
        if (empty($data)) {
            $data = $this->data;
        }
        if ($data[$this->pk]) {
            return $this->save($data);
        } else {
            return $this->add($data);
        }
    }
}

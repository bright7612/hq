<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.thinkphp.cn>
// +----------------------------------------------------------------------

/**
 * 前台配置文件
 * 所有除开系统级别的前台配置
 */

return array(

    // 预先加载的标签库
    'TAGLIB_PRE_LOAD' => 'OT\\TagLib\\Article,OT\\TagLib\\Think',

    /* 主题设置 */
    'DEFAULT_THEME' => 'default', // 默认模板主题名称
    /* 调试配置 */
    'SHOW_PAGE_TRACE' => flase,
    'SHOW_ERROR_MSG' =>  true,
    
    /* SESSION 和 COOKIE 配置 */
    'SESSION_PREFIX' => 'onethink_home', //session前缀
    'COOKIE_PREFIX' => 'onethink_home_', // Cookie前缀 避免冲突

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
        '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
        '__OFFICE__' => __ROOT__ . '/Public/office',
        '__ACTIVITY__' => __ROOT__ . '/Public/activity',
        '__WELFARE__' => __ROOT__ . '/Public/welfare',
        '__WARNING__' => __ROOT__ . '/Public/Hq/warning',
        '__DJ__' => __ROOT__ . '/Public/Hq/dangjian',
        '__BRAND__' => __ROOT__ . '/Public/Hq/brand',
    ),




    'NEED_VERIFY'=>true,//此处控制默认是否需要审核，该配置项为了便于部署起见，暂时通过在此修改来设定。
    'URL_MODEL' => '2',
    'SERVER_IP' => "139.196.184.43",
    /* 数据库配置 */
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => '139.196.184.43', // 服务器地址
    'DB_NAME'   => 'hqdj', // 数据库名
    'DB_USER'   => 'hqdj', // 用户名
    //'DB_PWD'    => 's7CcDWvpXEMVRBzG',  // 密码
    'DB_PWD'    => 'wiseljz123',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'ljz_', // 数据库表前缀

);


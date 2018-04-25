<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/25
 * Time: 15:30
 */

namespace Ljz\Controller;


use Think\Controller;

class UserController extends Controller
{
    public function login()
    {
        $mobile = $_POST["mobile"];
        $pwd = $_POST["pwd"];
        echo $mobile.":".$pwd.":".C("SERVER_IP");
    }
}
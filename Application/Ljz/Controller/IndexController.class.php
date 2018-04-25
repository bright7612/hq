<?php
namespace Ljz\Controller;

use Think\Controller;

class IndexController extends CommonController
{
    /**
     * from   1   微信风采榜
     *            2  微信心愿单
     *            3  微信我的
     */
    public function cml($session="")
    {
    	if($session){
    		session('name',$session);
			$name = $session;
    	}else{
    		$name = session('name');
    	}


		dump($session);
		dump($name);

		$this->assign("name", $name);
		$this->display();
	}

    public function index()
    {
        $signPackage = $this->getSignPackage();
        $from = I("get.from");
        $oid = I("get.oid");

        if (!$from) {
            $from = session('from');
        } else {
            session("from", $from);
        }

        if (!$oid) {
            $oid = session('oid');
        } else {
            session("oid", $oid);
        }

        if ($_GET['code']) {
            $accessToken = $this->accessToken($_GET['code']);
//			dump($accessToken) ;exit;
            if ($accessToken['expires_in']) {
                $accessToken['expire_time'] = time() + 7000;
                session("accessToken", $accessToken);
            }
            $userInfo = $this->getUserInfo($accessToken['access_token'], $accessToken['openid']);
        } else {
            $accessToken = session("accessToken");
            $userInfo = $this->getUserInfo($accessToken['access_token'], $accessToken['openid']);
        }
        cookie('wxOpenId', $userInfo['openid']);
        cookie('userInfo', $userInfo);

        if (!cookie('wxOpenId')) {
            //$redirect_uri = urlencode(C('SERVER_HOST'));
            $redirect_uri = urlencode("http://care.wiseljz.com/home/index/index/from/" . $from);
            $goindex = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx6cd4028d8d6de9ed&redirect_uri=' . $redirect_uri . '&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';

            echo "<script language=\"javascript\">";
            echo "document.location=\"" . $goindex . "\"";
            echo "</script>";
            exit;
        } else {
            $map['openId'] = cookie('wxOpenId');
            $search = M("ucenter_member")->where($map)->field('id,openId,mobile')->find();
            $cid = I("get.communityId");
            if($cid)
            {
                $wxcommunity["openid"] = cookie('wxOpenId');
                $wxcommunity["cTime"] = time();
                $wxcommunity["cTime_format"] = date("Y-m-d h:i:s",$wxcommunity["cTime"]);
                $wxcommunity["community"] = $cid;
                M("weixin_community")->add($wxcommunity);
            }
            if(!$cid)
            {
                $wxc =M("weixin_community")->where(array("openid"=>cookie('wxOpenId')))->order("id desc")->find();
                $cid =$wxc['community'];
            }
            if($cid)
            {
                session("communityId", $cid);
            }

            if ($search['id']) {
                newSession($search['id']);
                $info = idGetAllInfo($search['id']);
                session('info', $info['obj']);
                session("session", $info['obj']['session']);
                session('uid', $info['obj']['uid']);
                //session("community",$info['obj']['community']);

                if ($from == 1) {
                    session("role", 4);

                    if ($oid) {
                        session("oid", null);
                        $this->redirect('General/orderdetail', array("id" => $oid));
                        exit;
                    }
                    // $this->redirect('General/home');
                    //$this->redirect('General/hstyle');
                    $this->redirect('wall');
                    exit;
                } else if ($from == 2) {
                    if ($oid) {
                        session("oid", null);
                        $order = oidGetOrder($oid);
                        if ($order['status'] == 1) {
                            $this->redirect('Volunteer/resorting', array("oid" => $oid));
                            exit;
                        }

                        $this->redirect('Volunteer/orderdetail', array("id" => $oid));
                        exit;
                    }
                    //$this->redirect('wall');
                    $this->redirect('General/hwish');
                    exit;
                } else if ($from == 3) {
//                    if (session('info')['permission'] == 1)//已经是志愿者
//                    {
//                        //认证通过
//                        //$this->redirect('Volunteer/home');
//                        $this->redirect('General/hstyle');
//                    } else {
//                        $this->redirect('General/authVolunteer');
//                    }
                    $this->redirect('General/hmine');
                    exit;
                }else{
                    $this->redirect('General/hmine');
                    exit;
                }
            } else {
                if ($from == 3) {
                    $this->redirect("binding");
                    exit;
                }

                if (!$cid) {
                    $this->redirect('General/selectcommunity');
                    exit;
                }
                if ($from == 2) {
                    $this->redirect("General/hwish");
                } else {
                    $this->redirect("wall");
                }
                exit;
            }
        }
    }

    public function wall()
    {
        $this->assign('from', session('from'));
        $uid = session("uid");
        if ($uid) {
            $info = idGetAllInfo($uid)['obj'];
            if ($info) {
                $this->assign("role", $info['role'][0]['role_id']);
                $this->assign("uid", session('uid'));
                $this->assign("lv", $info['lv']);
                $this->assign("info", $info);
            }
        }

        $this->display();
    }

    public function listWall($page = 1, $rows = 10, $order = 0)
    {
        $p["rows"] = $rows;
        $p['page'] = $page;
        $p['order'] = $order;
        $community = session('info')['community'];
        $cid = $community['id'];
        if (!$cid) {
            $cid = cookie('communityId');
        }
        if ($cid) {
            $p['community'] = $cid;
        }
        $res = request_post(C("SERVER_IP") . "/api/wx/wall", $p);
        $res = json_decode($res, true);
        if (!$res['list']) {
            echo "nomore";
            exit;
        }
        if (session('uid')) {
            $this->assign('twinIds', uidGetTwinVolunteerIds(session('uid')));
        }
        $this->assign("list", $res['list']);
        $this->display();
    }


    public function code($mobile)
    {
        $dataBinding['mobile'] = $mobile;

        $mobileExist = M("ucenter_member")->where($dataBinding)->find();
//		echo $mobileExist['id'];
        if ($mobileExist['id']) {

            if (!isMobileNew($dataBinding['mobile'])) {
                echo apiResault(1001, "电话号码格式不正确");
                exit;
            }

            $M = M("Register");
            $infoexist = $M->where($dataBinding)->find();
            $dataBinding['autocode'] = randCode(6, 1);
            $dataBinding['time'] = date('Y-m-d H:i:s', time());

            $message = '验证码：' . $dataBinding['autocode'] . '， 欢迎您注册！';

            $checkmobile['mobile'] = $mobile;

            $mobileexist = $M->where($checkmobile)->order('id desc')->find();
            if ($mobileexist['time']) {
                $timestr = strtotime($mobileexist['time']);
                $nowtime = time();
                $timeresult = $nowtime - $timestr;
                if ($timeresult < 90) {
                    echo apiResault(1002, "90秒内短信不能重发");
                    exit;
                }
            }

            $result = smsace($dataBinding['mobile'], $message);


            if ($result) {
                $dataBinding['result'] = $result;
                $resultregister = $M->add($dataBinding);

                $this->assign("mobile", $mobile);
                $this->display("");
            } else {
                echo apiResault(1003, "短信验证码发送接口异常");
                exit;
            }
        } else {
            $this->display("register");
        }
    }


    public function getCode($mobile)
    {
        $dataBinding['mobile'] = $mobile;

        $mobileExist = M("ucenter_member")->where($dataBinding)->find();
//		echo $mobileExist['id'];
        if ($mobileExist['id']) {
            if (!isMobileNew($dataBinding['mobile'])) {
                echo apiResault(1001, "电话号码格式不正确");
                exit;
            }

            $M = M("Register");
            $infoexist = $M->where($dataBinding)->find();
            $dataBinding['autocode'] = randCode(6, 1);
            $dataBinding['time'] = date('Y-m-d H:i:s', time());

            $message = '验证码：' . $dataBinding['autocode'] . '， 欢迎您注册！';

            $checkmobile['mobile'] = $mobile;

            $mobileexist = $M->where($checkmobile)->order('id desc')->find();
            if ($mobileexist['time']) {
                $timestr = strtotime($mobileexist['time']);
                $nowtime = time();
                $timeresult = $nowtime - $timestr;
                if ($timeresult < 90) {
                    echo apiResault(1002, "90秒内短信不能重发");
                    exit;
                }
            }

            $result = smsace($dataBinding['mobile'], $message);


            if ($result) {
                $dataBinding['result'] = $result;
                $resultregister = $M->add($dataBinding);
                echo apiResault(1, "验证码发送成功");
            } else {
                echo apiResault(1003, "短信验证码发送接口异常");
                exit;
            }
        } else {
            echo apiResault(10001, "您还没有注册过，进入注册页面");
        }
    }


    public function bind($mobile = "", $code = "")
    {
        $map['mobile'] = $mobile;
        $mobileexist = M("register")->where($map)->order('id desc')->find();
        if ($code != $mobileexist['autocode']) {
            echo apiResault(1101, "验证码不正确");
            exit;
        }

        $data['openId'] = cookie('wxOpenId');
        $result = M('ucenter_member')->where($map)->save($data);
		dump($data);
        echo apiResault(1, "绑定成功");
    }

    public function register($mobile = "")
    {
        $this->assign("mobile", $mobile);
        $this->display();
    }


    public function regist($mobile = "", $code = "", $pwd = "")
    {
        $p['mobile'] = $mobile;
        $p['vcode'] = $code;
        $p['password'] = $pwd;
        $p['openid'] = cookie('wxOpenId');
        $res = request_post(C('SERVER_IP') . "/api/member/register", $p);
        echo $res;
    }

    public function getRegistCode($mobile = "")
    {
        $p['mobile'] = $mobile;
        echo request_post(C('SERVER_IP') . "/api/member/sms", $p);
    }

    public function binding()
    {
        $this->display();
    }


    public function template($openid = 'oL7SRwc-NqPFYLNelegpEZpOZEgM', $title, $content, $remark, $type = "", $link = "")
    {


        $accessToken = $this->getAccessToken($url);
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessToken;

        $param ['touser'] = $openid;

        $param ['template_id'] = 'sGjxgdaY8XCpiUbVsnPLzGRM7tGADrk5aE3ykkBYllw';

        $param ['url'] = 'http://group.acebridge.net/app.html';//直接下载页或跳转app


        if ($link) {
            $param ['url'] = $link;
        }

        $param ['data']['first']['value'] = $title;
        $param ['data']['first']['color'] = "#173177";

        $param ['data']['keyword1']['value'] = $content;
        $param ['data']['keyword1']['color'] = "#173177";

        $param ['data']['keyword2']['value'] = date("Y-m-d H:i:s", time());
        $param ['data']['keyword2']['color'] = "#173177";

        $param ['data']['remark']['value'] = $remark;
        $param ['data']['remark']['color'] = "#173177";

//		http://group.acebridge.net/home/wap/template.html?openid=ox6u9jnqK_fmOhv4eW84hWB-PxkA&title=您关注的项目有了更新&content=众筹项目3934已有34人关注&remark=详情请登录网站或APP查看
        $res = post_data($url, $param);
        $res = json_encode($res);
        echo $res;
        exit;

    }


}
<?php


namespace Hq\Controller;

use Think\Controller;

/**
 * Class IndexController
 * @package Shop\Controller
 * @郑钟良
 */
class IndexController extends Controller
{
    protected $goods_info = 'id,goods_name,goods_ico,goods_introduct,tox_money_need,goods_num,changetime,status,createtime,category_id,is_new,sell_num';

    /**
     * 超市初始化
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _initialize()
    {
        $tree = D('shopCategory')->getTree();
        $this->assign('tree', $tree);
        if (is_login()) {
            $this->assign('my_tox_money', getMyToxMoney());
        }
        $this->assign('tox_money_name', getToxMoneyName());
        $hot_num = D('shop_config')->where(array('ename' => 'min_sell_num'))->getField('cname');
        $this->assign('hot_num', $hot_num);
        $menu_list = array(
            'left' =>
                array(
                    array('tab' => 'home', 'title' => '首页', 'href' => U('shop/index/index')),
                    array('tab' => 'all', 'title' => '所有商品', 'href' => U('shop/index/goods')),
                ),
            'right' =>
                array(
                    array('tab' => 'orders', 'title' => '我的订单', 'href' => U('shop/index/mygoods'), 'icon' => 'list-alt'),
                    array('tab' => 'cart', 'title' => '我的购物车', 'href' => U('shop/index/cart'), 'icon' => 'list-alt'),
                    array('tab' => 'money', 'title' => '当前' . getToxMoneyName() . '：' . getMyToxMoney(), 'icon' => 'stats',
                    )
                )
        );
        foreach ($tree as $category) {
            $menu = array('tab' => 'category_' . $category['id'], 'title' => $category['title'], 'href' => U('shop/index/goods', array('category_id' => $category['id'])));
            if ($category['_']) {
                $menu['children'][] = array( 'title' => '全部', 'href' => U('shop/index/goods', array('category_id' => $category['id'])));
                foreach ($category['_'] as $child)
                    $menu['children'][] = array( 'title' => $child['title'], 'href' => U('shop/index/goods', array('category_id' => $child['id'])));
            }
            $menu_list['left'][] = $menu;
        }


        $this->assign('sub_menu', $menu_list);
        $this->assign('current', 'home');
        $this->setTitle('超市');
    }

    /**
     * 商品页初始化
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function _goods_initialize()
    {
        $shop_address = D('shop_address')->where('uid=' . is_login())->find();
        $this->assign('shop_address', $shop_address);
    }

    /**
     * 超市首页
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function index()
    {
        $this->_goods_initialize();
        //新品上架
        $map['is_new'] = 1;
        $map['status'] = 1;
        $goods_list_new = D('shop')->where($map)->order('changetime desc')->limit(8)->field($this->goods_info)->select();
        $this->assign('contents_new', $goods_list_new);

        //热销商品
        $hot_num = D('shop_config')->where(array('ename' => 'min_sell_num'))->getField('cname');
        $map_hot['sell_num'] = array('egt', $hot_num);
        $map_hot['status'] = 1;
        $goods_list_hot = D('shop')->where($map_hot)->order('sell_num desc')->limit(8)->field($this->goods_info)->select();
        $this->assign('contents_hot', $goods_list_hot);
        $this->display();
    }


    /**
     * 商品页
     * @param int $page
     * @param int $category_id
     * @author 郑钟良<zzl@ourstu.com>
    */
    public function cart()
    {
        $this->_goods_initialize();
		$map['uid']=is_login();
		$allmoney = 0;
        $goods_list = D('shop_cart')->where($map)->order('createtime desc')->select();
		foreach ($goods_list as $k => $v) {
			$condition['id'] = $v['goods_id'];
			$goods_info = M('shop')->where($condition)->find();
			$goods_list[$k]['goods_name'] = $goods_info['goods_name'];
			$conditionpic['id'] = $goods_info['goods_ico'];	
			$goodsico_info = M('picture')->where($conditionpic)->field('path')->find();	
			$goods_list[$k]['goods_ico'] = $goodsico_info['path'];
			$goods_list[$k]['tox_money_need'] = $goods_info['tox_money_need'];	
			$allmoney+=$goods_info['tox_money_need']*$v['goods_num'];
		}
		//dump($goods_list);
        $this->assign('contents', $goods_list);
		$this->assign('allmoney', $allmoney);
        $this->assign('totalPageCount', $totalCount);
	

        $this->setTitle('{$category_name|op_t}' . ' 购物车');
        $this->setKeywords('{$category_name|op_t}' . ', 购物车');

		//dump($my_tox_money);
        $this->display('cart');
    }


    /**
     * 商品页
     * @param int $page
     * @param int $category_id
     * @author 郑钟良<zzl@ourstu.com>
    */
    public function goods($page = 1, $category_id = 0)
    {
        $this->_goods_initialize();
        $category_id = intval($category_id);
        $goods_category = D('shopCategory')->find($category_id);
        if ($category_id != 0) {
            $category_id = intval($category_id);
            $goods_categorys = D('shop_category')->where("id=%d OR pid=%d", array($category_id, $category_id))->limit(999)->select();
            $ids = array();
            foreach ($goods_categorys as $v) {
                $ids[] = $v['id'];
            }
            $map['category_id'] = array('in', implode(',', $ids));
        }
        $map['status'] = 1;
        $goods_list = D('shop')->where($map)->order('createtime desc')->page($page, 16)->field($this->goods_info)->select();
        $totalCount = D('shop')->where($map)->count();
        foreach ($goods_list as &$v) {
            $v['category'] = D('shopCategory')->field('id,title')->find($v['category_id']);
        }
        unset($v);
        $this->assign('contents', $goods_list);
        $this->assign('totalPageCount', $totalCount);
        $top_category_id = $goods_category['pid'] == 0 ? $goods_category['id'] : $goods_category['pid'];
        $this->assign('top_category', $top_category_id);
        $this->assign('category_id', $category_id);
        if ($top_category_id == $category_id) {
            $cate_name = $goods_category['title'];
            $this->assign('category_name', $cate_name);
        } else {
            $cate_name = D('shopCategory')->where(array('id' => $top_category_id))->getField('title');
            $this->assign('category_name', $cate_name);
            $this->assign('child_category_name', $goods_category['title']);
        }
        $this->setTitle('{$category_name|op_t}' . ' 超市');
        $this->setKeywords('{$category_name|op_t}' . ', 超市');
        if($category_id==0){
            $this->assign('current', 'all');
        }else{
            $this->assign('current', 'category_'.$top_category_id);
        }
       // dump( 'category_'.intval($category_id));exit;

        $this->display();
    }

    /**
     * 商品详情页
     * @param int $id
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function goodsDetail($id = 0)
    {
        $this->_goods_initialize();
        $goods = D('shop')->find($id);
        if (!$goods) {
            $this->error('404 not found');
        }

        $category = D('shopCategory')->find($goods['category_id']);
        $top_category_id = $category['pid'] == 0 ? $category['id'] : $category['pid'];
        $this->assign('top_category', $top_category_id);
        $this->assign('category_id', $category['id']);
        if ($top_category_id == $category['id']) {
            $this->assign('category_name', $category['title']);
        } else {
            $this->assign('category_name', D('shopCategory')->where(array('id' => $top_category_id))->getField('title'));
            $this->assign('child_category_name', $category['title']);
        }
        $this->assign('content', $goods);
        //同类对比
        $goods_categorys_ids = D('shop_category')->where("id=%d OR pid=%d", array($category['id'], $category['id']))->limit(999)->field('id')->select();
        foreach ($goods_categorys_ids as &$v) {
            $v = $v['id'];
        }
        $map['category_id'] = array('in', $goods_categorys_ids);
        $map['status'] = 1;
        $map['id'] = array('neq', $id);
        $same_category_goods = D('shop')->where($map)->limit(3)->order('sell_num desc')->field($this->goods_info)->select();
        $this->assign('contents_same_category', $same_category_goods);
        //最近浏览
        if (is_login()) {
            //关联查询最近浏览
            $sql = "SELECT a." . $this->goods_info . " FROM `" . C('DB_PREFIX') . "shop` AS a , `" . C('DB_PREFIX') . "shop_see` AS b WHERE ( b.`uid` =" . is_login() . " ) AND ( b.`goods_id` <> '" . $id . "' ) AND ( a.`status` = 1 )AND(a.`id` =b.`goods_id`) ORDER BY b.update_time desc LIMIT 3";
            $Model = new \Think\Model();
            $goods_see_list = $Model->query($sql);
            $this->assign('goods_see_list', $goods_see_list);
            //添加最近浏览
            $map_see['uid'] = is_login();
            $map_see['goods_id'] = $id;
            $rs = D('ShopSee')->where($map_see)->find();
            if ($rs) {
                $data['update_time'] = time();
                D('ShopSee')->where($map_see)->save($data);
            } else {
                $map_see['create_time'] = $map_see['update_time'] = time();
                D('ShopSee')->add($map_see);
            }
        }

        $this->setTitle('{$content.goods_name|op_t}' . ' 超市');
        $this->setKeywords('{$content.goods_name|op_t}' . ', 超市');
        $this->display();
    }

    /**
     * 购物地址修改
     * @param int $id
     * @param int $num
     * @author cml
     */
    public function addressEdit($name = '', $address = '', $zipcode = '', $phone = '', $address_id = '')
    {

        $address = op_t($address);
        $address_id = intval($address_id);
        if (!is_login()) {
            $this->error('请先登录！');
        }


        //用户地址处理
        if ($name == '' || !preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $name)) {
            $this->error('请输入正确的用户名');
        }
        if ($address == '') {
            $this->error('请填写收货地址');
        }
        if ($zipcode == '' || strlen($zipcode) != 6 || !is_numeric($zipcode)) {
            $this->error('请正确填写邮编');
        }
        if ($phone == '' || !preg_match("/^1[3458][0-9]{9}$/", $phone)) {
            $this->error('请正确填写手机号码');
        }
        $shop_address['phone'] = $phone;
        $shop_address['name'] = $name;
        $shop_address['address'] = $address;
        $shop_address['zipcode'] = $zipcode;
        if ($address_id) {
            $address_save = D('shop_address')->where(array('id' => $address_id))->save($shop_address);
            if ($address_save) {
                D('shop_address')->where(array('id' => $address_id))->setField('change_time', time());
            }
            $data['address_id'] = $address_id;
        } else {
            $shop_address['uid'] = is_login();
            $shop_address['create_time'] = time();
            $data['address_id'] = D('shop_address')->add($shop_address);
        }
        //处理结束
         $this->success('地址修改成功', $_SERVER['HTTP_REFERER']);

    }


    public function goodsPing($id,$markfinfo,$star){

		$map = array('id' => $id);
		$data['markfinfo'] = $markfinfo;
		$data['star'] = $star;
				
		$info = M("shop_buy")->where($map)->save($data);
//		echo M()->getlastsql();
		if($info!=FALSE){
			$this->success('评价成功！', $_SERVER['HTTP_REFERER']);
		}else{
			$this->error('评价失败！');
		}
	}
	
    public function goodsTui($id,$markfinfo){

		$map = array('id' => $id);
		$data['markfinfo'] = $markfinfo;
		$data['status'] = 9;		
		$info = M("shop_buy")->where($map)->save($data);

		if($info!=FALSE){
			$this->success('退货申请成功，请耐心等待客服联系！', $_SERVER['HTTP_REFERER']);
		}else{
			$this->error('退货申请失败，请稍后再试！');
		}
	}	


    public function goodsMinus($id){
		$map = array('id' => $id);
		$info = M("Shop_cart")->field('goods_num')->where($map)->find();
		if($info['goods_num']){
			$result = M("Shop_cart")->where($map)->setDec('goods_num');
		}
		
		
		if($result){
			$info2 = M("Shop_cart")->field('goods_num')->where($map)->find();
			$this->success($info2['goods_num']);
		}else{
			$this->error('操作失败');
		}
	}


    public function goodsPlus($id){
		$map = array('id' => $id);
		$result = M("Shop_cart")->where($map)->setInc('goods_num');
		
		if($result){
			$info = M("Shop_cart")->field('goods_num')->where($map)->find();
			$this->success($info['goods_num']);
		}else{
			$this->error('操作失败');
		}
	}
	
	public function delCart($id){
		$map = array('id' => $id);
		$result = M("Shop_cart")->where($map)->del('goods_num');		
		if($result){
			$this->success('删除成功', $_SERVER['HTTP_REFERER']);
		}else{
			$this->error('操作失败');
		}
	}

    /**
     * 购买购物车商品
     * @param int $id
     * @param int $num
     * @author cml
     */
    public function cartBuy($id, $goods_num,$address_id,$allmoney)
    {

		//dump($id);
		//dump($goods_num);
		//exit;
		if(1){
			$this->error('您的账号异地登录，请重新登录。');			
		}
        $address_id = intval($address_id);
		if(!$address_id){
			$this->error('商品派送地址没有填写！');
			
		}
        if (!is_login()) {
            $this->error('请先登录！');
        }
		
        $my_tox_money = getMyToxMoney();
        if ($allmoney > $my_tox_money) {
            $this->error('你的' . getToxMoneyName() . '不足');
        }

		foreach ($id as $k => $v) {
			$num = $goods_num[$k];
	        $goods = D('shop')->where('id=' . $v)->find();
	        if ($goods) {
	            if ($num > $goods['goods_num']) {
	                $this->error($goods['goods_name'].'余量不足<br>剩余数量 '.$goods['goods_num']);
	            }
			}else {
	            $this->error('请选择要购买的商品');
	        }
		}
	

		
		foreach ($id as $k => $v) {

			$num = $goods_num[$k];
	        $goods = D('shop')->where('id=' . $v)->find();
	        if ($goods) {
	

				$data['address_id'] = $address_id;
	            $data['goods_id'] = $goods['id'];
	            $data['goods_num'] = $num;
	            $data['status'] = 0;
	            $data['uid'] = is_login();
	            $data['createtime'] = time();
	
	
	            D('member')->where('uid=' . is_login())->setDec('tox_money', $tox_money_need);
	            $res = D('shop_buy')->add($data);
	            if ($res) {
	                //商品数量减少,已售量增加
	                D('shop')->where('id=' . $id)->setDec('goods_num', $num);
	                D('shop')->where('id=' . $id)->setInc('sell_num', $num);
					//删除购物车
					$cartdel['goods_id'] = $v;
		            $cartdel['uid'] = is_login();
					//dump($cartdel['goods_id']);
					//exit;
					M('shop_cart')->where($cartdel)->delete();
					
					$messagex .= ' 数量 '.$num.' 的'.$goods['goods_name'].'，';

	            } else {
	                $this->error('购买失败！');
	            }
	        } 

		}
		
        //发送系统消息
        $message = $messagex. "购买成功，请等待发货。";
        D('Message')->sendMessageWithoutCheckSelf(is_login(), $message, '购买成功通知', U('Shop/Index/myGoods', array('status' => '0')));

        //超市记录
        $shop_log['message'] = '用户[' . is_login() . ']' . query_user('nickname', is_login()) . '在' . time_format($data['createtime']) . '购买了'.$messagex;
        $shop_log['uid'] = is_login();
        $shop_log['create_time'] = $data['createtime'];
        D('shop_log')->add($shop_log);

        $this->success('购买成功！花费了' . $allmoney . getToxMoneyName(), $_SERVER['HTTP_REFERER']);		

    }
    
    /**
     * 购买商品
     * @param int $id
     * @param int $num
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function goodsBuy($id = 0, $num = 1, $name = '', $address = '', $zipcode = '', $phone = '', $address_id = '')
    {
        $address = op_t($address);
        $address_id = intval($address_id);
        if (!is_login()) {
            $this->error('请先登录！');
        }
        $goods = D('shop')->where('id=' . $id)->find();
        if ($goods) {

            //验证开始
            //判断商品余量
            if ($num > $goods['goods_num']) {
                $this->error('商品余量不足');
            }

            //扣tox_money
            $tox_money_need = $num * $goods['tox_money_need'];
            $my_tox_money = getMyToxMoney();
            if ($tox_money_need > $my_tox_money) {
                $this->error('你的' . getToxMoneyName() . '不足');
            }

            //用户地址处理
            if ($name == '' || !preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $name)) {
                $this->error('请输入正确的用户名');
            }
            if ($address == '') {
                $this->error('请填写收货地址');
            }
            if ($zipcode == '' || strlen($zipcode) != 6 || !is_numeric($zipcode)) {
                $this->error('请正确填写邮编');
            }
            if ($phone == '' || !preg_match("/^1[3458][0-9]{9}$/", $phone)) {
                $this->error('请正确填写手机号码');
            }
            $shop_address['phone'] = $phone;
            $shop_address['name'] = $name;
            $shop_address['address'] = $address;
            $shop_address['zipcode'] = $zipcode;
            if ($address_id) {
                $address_save = D('shop_address')->where(array('id' => $address_id))->save($shop_address);
                if ($address_save) {
                    D('shop_address')->where(array('id' => $address_id))->setField('change_time', time());
                }
                $data['address_id'] = $address_id;
            } else {
                $shop_address['uid'] = is_login();
                $shop_address['create_time'] = time();
                $data['address_id'] = D('shop_address')->add($shop_address);
            }
            //验证结束

            $data['goods_id'] = $id;
            $data['goods_num'] = $num;
            $data['status'] = 0;
            $data['uid'] = is_login();
            $data['createtime'] = time();


            D('member')->where('uid=' . is_login())->setDec('tox_money', $tox_money_need);
            $res = D('shop_buy')->add($data);
            if ($res) {
                //商品数量减少,已售量增加
                D('shop')->where('id=' . $id)->setDec('goods_num', $num);
                D('shop')->where('id=' . $id)->setInc('sell_num', $num);
                //发送系统消息
                $message = '数量 '.$num.' 的'.$goods['goods_name'] . "购买成功，请等待发货。";
                D('Message')->sendMessageWithoutCheckSelf(is_login(), $message, '购买成功通知', U('Shop/Index/myGoods', array('status' => '0')));

                //超市记录
                $shop_log['message'] = '用户[' . is_login() . ']' . query_user('nickname', is_login()) . '在' . time_format($data['createtime']) . '购买了数量 '.$num.' 的商品<a href="index.php?s=/Shop/Index/goodsDetail/id/' . $goods['id'] . '.html" target="_black">' . $goods['goods_name'] . '</a>';
                $shop_log['uid'] = is_login();
                $shop_log['create_time'] = $data['createtime'];
                D('shop_log')->add($shop_log);

                $this->success('购买成功！花费了' . $tox_money_need . getToxMoneyName(), $_SERVER['HTTP_REFERER']);
            } else {
                $this->error('购买失败！');
            }
        } else {
            $this->error('请选择要购买的商品');
        }
    }


    /**
     * 商品购物车
     * @param int $id
     * @param int $num
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function goodscart($id = 0, $num = 1)
    {
        $address = op_t($address);
        $address_id = intval($address_id);
        if (!is_login()) {
            $this->error('请先登录！');
        }
        $goods = D('shop')->where('id=' . $id)->find();
		//dump($goods);
        if ($goods) {

            //验证开始
            //判断商品余量
            if ($num > $goods['goods_num']) {
                $this->error('商品余量不足');
            }

            //判断tox_money够不够
            $tox_money_need = $num * $goods['tox_money_need'];
            $my_tox_money = getMyToxMoney();
            if ($tox_money_need > $my_tox_money) {
                $this->error('你的' . getToxMoneyName() . '不足');
            }
            //验证结束
            
            $data['goods_id'] = $id;
            $data['uid'] = is_login();
			
			$exist = M('shop_cart')->where($data)->find();
			if($exist){
				$datasave['edittime'] = time();
				$datasave['goods_num'] = $num+$exist['goods_num'] ;
				$res = M('shop_cart')->where($data)->save($datasave);
				if ($res) {
	                $this->success('加入购物车成功！数量增加'.$num.'预计要花费' . $tox_money_need . getToxMoneyName(), $_SERVER['HTTP_REFERER']);
	            } else {
	                $this->error('加入购物车失败！');
	            }
			}else{
				$data['createtime'] = time();
				$data['goods_num'] = $num ;
				$res = D('shop_cart')->add($data);
	            if ($res) {
	                $this->success('加入购物车成功！预计要花费' . $tox_money_need . getToxMoneyName(), $_SERVER['HTTP_REFERER']);
	            } else {
	                $this->error('加入购物车失败！');
	            }
				
			}

            $data['createtime'] = time();



        } else {
            $this->error('请选择要加入购物车的商品');
        }
		
    }

    /**
     * 商品购物车
     * @param int $id
     * @param int $num
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function apiGoodsCart($id = 0, $num = 1)
    {
        $address = op_t($address);
        $address_id = intval($address_id);
        if (!is_login()) {
            $this->error('请先登录！');
        }
        $goods = D('shop')->where('id=' . $id)->find();
		//dump($goods);
        if ($goods) {

            //验证开始
            //判断商品余量
            if ($num > $goods['goods_num']) {
                $this->error('商品余量不足');
            }

            //判断tox_money够不够
            $tox_money_need = $num * $goods['tox_money_need'];
            $my_tox_money = getMyToxMoney();
            if ($tox_money_need > $my_tox_money) {
                $this->error('你的' . getToxMoneyName() . '不足');
            }
            //验证结束
            
            $data['goods_id'] = $id;
            $data['uid'] = is_login();
			
			$exist = M('shop_cart')->where($data)->find();
			if($exist){
				$datasave['edittime'] = time();
				$datasave['goods_num'] = $num+$exist['goods_num'] ;
				$res = M('shop_cart')->where($data)->save($datasave);
				if ($res) {
	                $this->success('加入购物车成功！数量增加'.$num.'预计要花费' . $tox_money_need . getToxMoneyName(), $_SERVER['HTTP_REFERER']);
	            } else {
	                $this->error('加入购物车失败！');
	            }
			}else{
				$data['createtime'] = time();
				$res = D('shop_cart')->add($data);
	            if ($res) {
	                $this->success('加入购物车成功！预计要花费' . $tox_money_need . getToxMoneyName(), $_SERVER['HTTP_REFERER']);
	            } else {
	                $this->error('加入购物车失败！');
	            }
				
			}

            $data['createtime'] = time();



        } else {
            $this->error('请选择要加入购物车的商品');
        }
		
    }

 

    /**
     * 个人商品页
     * @param int $page
     * @param     $status
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function myGoods($page = 1, $status = 0)
    {
        if (!is_login()) {
            $this->error('你还没有登录，请先登录');
        }
        $map['status'] = $status;
        $map['uid'] = is_login();
        $goods_buy_list = D('shop_buy')->where($map)->page($page, 16)->order('createtime desc')->select();
        $totalCount = D('shop_buy')->where($map)->count();
        foreach ($goods_buy_list as &$v) {
            $v['goods'] = D('shop')->where('id=' . $v['goods_id'])->field($this->goods_info)->find();
            $v['category'] = D('shopCategory')->field('id,title')->find($v['goods']['category_id']);
        }
        unset($v);
        $this->assign('contents', $goods_buy_list);
        $this->assign('totalPageCount', $totalCount);
        $this->assign('status', $status);
        $this->assign('current', 'orders');
        $this->display();

    }
}
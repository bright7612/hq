<?php
namespace Ljz\Controller;
use Think\Controller;
class CommonController extends Controller {
	public function _initialize($appId, $appSecret) {

		$this -> appId = 'wx6cd4028d8d6de9ed';
		$this -> appSecret = '3463acc00b6a3272c7790327b92320df';
        $this->appId = 'wx22e0907c8ee48e0c';
        $this->appSecret = 'fa50269e54ead0e2051b5440568ad107';
		
		$server_host = '/';
		C('SERVER_HOST',$server_host);
		C('URL_MODEL',2);

	}
	
	public function getSignPackage() {
		$jsapiTicket = $this -> getJsApiTicket();

		// 注意 URL 一定要动态获取，不能 hardcode.
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$timestamp = time();
		$nonceStr = $this -> createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array("appId" => $this -> appId, "nonceStr" => $nonceStr, "timestamp" => $timestamp, "url" => $url, "signature" => $signature, "rawString" => $string);
		return $signPackage;
	}

	public function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	public function getJsApiTicket() {
		// jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = json_decode($this -> get_php_file("wx/jsapi_ticket.php"));
		if ($data -> expire_time < time()) {
			$accessToken = $this -> getAccessToken();
			// 如果是企业号用以下 URL 获取 ticket
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			$res = json_decode($this -> httpGet($url));
			$ticket = $res -> ticket;
			if ($ticket) {
				$data -> expire_time = time() + 7000;
				$data -> jsapi_ticket = $ticket;
				$this -> set_php_file("wx/jsapi_ticket.php", json_encode($data));
			}
		} else {
			$ticket = $data -> jsapi_ticket;
		}

		return $ticket;
	}

	public function getAccessToken() {
		// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = json_decode($this -> get_php_file("wx/access_token.php"));
		if ($data -> expire_time < time()) {
			// 如果是企业号用以下URL获取access_token
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
			$res = json_decode($this -> httpGet($url));
			$access_token = $res -> access_token;
			if ($access_token) {
				$data -> expire_time = time() + 3600;
				$data -> access_token = $access_token;
				$this -> set_php_file("wx/access_token.php", json_encode($data));
			}
		} else {
			$access_token = $data -> access_token;
		}
		return $access_token;
	}
	
	public function getAccessTokenOut() {
		// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = json_decode($this -> get_php_file("wx/access_token.php"));
		if ($data -> expire_time < time()) {
			// 如果是企业号用以下URL获取access_token
			// $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
			$res = json_decode($this -> httpGet($url));
			$access_token = $res -> access_token;
			if ($access_token) {
				$data -> expire_time = time() + 3600;
				$data -> access_token = $access_token;
				$this -> set_php_file("wx/access_token.php", json_encode($data));
			}
		} else {
			$access_token = $data -> access_token;
		}
		echo $access_token;
	}
	

	public function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
		// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($curl, CURLOPT_URL, $url);

		$res = curl_exec($curl);
		curl_close($curl);

		return $res;
	}

	public function get_php_file($filename) {
		return trim(substr(file_get_contents($filename), 15));
	}

	public function set_php_file($filename, $content) {
		$fp = fopen($filename, "w");
		fwrite($fp, "<?php exit();?>" . $content);
		fclose($fp);
	}
	
	
	public function accessToken($code) {
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->appId&secret=$this->appSecret&code=" . $code . "&grant_type=authorization_code";
		$res = json_decode($this -> httpGet($url), true);

		$url2 = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=$this->appId&grant_type=refresh_token&refresh_token=" . $res['refresh_token'];
		$res2 = json_decode($this -> httpGet($url2), true);

		return $res;
	}

	public function getUserInfo($access_token, $openid) {
		$url = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
		$res = json_decode($this -> httpGet($url), true);
		return $res;
	}
	
	
	
}
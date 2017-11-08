<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------

namespace com\weixin;

class WeixinAuthUser extends WeixinAuth{
	
	public function __construct($options=[]){
		parent::__construct($options);
	}

	/**
	 * oauth 授权跳转接口-获取用户OpenId
	 * @param string $callback 回调URI
	 * @return string
	 */
	public function getUserOpenid($code=''){
		//通过code获得openid
		if (empty($code)){
			//读取缓存
			$cacheName = "usertoken{$this->appid}";
			$data = $this->getCache($cacheName);
			if($data){
				return $data['openid'];
			}
			//触发微信返回code码
			$callback = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
			$this->userAuthorize($callback);
		}else{
			$userToken = $this->getUserToken($code);
			if($userToken){
				return $userToken['openid'];
			}
			return false;
		}
	}
	
	public function getUserToken($code){
		//读取缓存
		$cacheName = "usertoken{$this->appid}";
		$data = $this->getCache($cacheName);
		if($data){
			return $data;
		}
		//code为空则返回错误
		if(empty($code)){
			return false;
		}
		
		$api = [
			'url'	=> 	"https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->appsecret}&code={$code}&grant_type=authorization_code",
		];
		
		//判断是否是平台授权
		if($this->auth_refresh_token){
			$api['url'] = "https://api.weixin.qq.com/sns/oauth2/component/access_token?appid={$this->appid}&code={$code}&grant_type=authorization_code";
			$api['url'] .= '&component_appid='.$this->component_appid;
			$api['url'] .= '&component_access_token='.$this->getComponentToken();
		}
		
		$res = $this->http_get($api['url']);
		if ($res){
			$json = json_decode($res,true);
			if (empty($json) || isset($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			$expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
			$this->setCache($cacheName,$json,$expire);
			return $json;
		}
		return false;
	}
	
	public function getUserInfo($code=''){
		//读取接口调用凭证
		$tokenInfo = $this->getUserToken($code);
		if(empty($tokenInfo)){
			return false;
		}
		//接口数据
		$api = [
			'url'	=> 	"https://api.weixin.qq.com/cgi-bin/user/info?access_token={$tokenInfo['access_token']}&openid={$tokenInfo['openid']}&lang=zh_CN",
		];
		
		//接口调用
		$res = $this->http_get($api['url']);
		if ($res){
			$json = json_decode($res,true);
			if (empty($json) || isset($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			return $json;
		}
		return false;
	}
	
	/**
	 * oauth 授权跳转接口-获取用户信息
	 * @param string $callback 回调URI
	 * @return string
	 */
	public function userAuthorize($callback,$scope='snsapi_base',$state=''){
		$api = [
			'url'	=> 	"https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->appid}&redirect_uri={$callback}&response_type=code&scope={$scope}&state={$state}",
		];
		//判断是否是平台授权
		if($this->auth_refresh_token){
			$api['url'] .= '&component_appid='.$this->component_appid;
		}
		$api['url'] .= '#wechat_redirect';
		Header("Location: {$api['url']}");
		exit();
	}
	
	public function getJsapiConfig($url){
		$config['nonceStr'] = $this->getNnonceStr(32);
		$config['jsapi_ticket'] = $this->getJsapiTicket();
		if(!$config['jsapi_ticket']){
			return false;
		}
		$config['timestamp'] = time();
		$config['url'] = $url;
		$config['signature'] = $this->getSignature($config);
		$config['appId'] = $this->appid;
		$config['debug'] = false;
		$config['jsApiList'] = ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo'];
		return $config;
	}
	
	/**
	 * 获取ticket
	 * https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=ACCESS_TOKEN&type=jsapi
	 * 正确返回:{"ticket":"ACCESS_TOKEN","expires_in":7200}
	 * 错误返回:{"errcode":40013,"errmsg":"invalid appid"}
	 * @param string $access_token 接口凭证
	 */
	protected function getJsapiTicket(){
		//读取缓存
		$cacheName  = "jsapiticket{$this->appid}";
		$data = $this->getCache($cacheName);
		if ($data)  {
			return $data;
		}
		//获取接口调用凭证
		$token = $this->getToken();
		if(empty($token)){
			return false;
		}
		//接口参数
		$api = [
			'url'	=> 	"https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token={$token}&type=jsapi",
		];
		//接口调用
		$res = $this->http_get($api['url']);
		if ($res){
			$json = json_decode($res,true);
			if($json['errmsg']=='ok'){
				$data = $json['ticket'];
				$expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
				$this->setCache($cacheName,$data,$expire);
				return $data;
			}else{
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
		}
		return false;
	}
}

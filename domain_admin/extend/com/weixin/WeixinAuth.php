<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------

namespace com\weixin;

class WeixinAuth extends Weixin{
	
	protected $component_appid = '';
	protected $component_appsecret = '';
	protected $auth_refresh_token = '';
	//授权类型 0：url授权 1：第三方授权
	protected $auth_type = 0;
	
	
	public function __construct($options=[]){
		$this->debug = false;
 		parent::__construct($options);
 		$this->component_appid 		= isset($options['component_appid'])?$options['component_appid']:'';
 		$this->component_appsecret 	= isset($options['component_appsecret'])?$options['component_appsecret']:'';
 		$this->auth_refresh_token 	= isset($options['authorizer_refresh_token'])?$options['authorizer_refresh_token']:'';
	}
	
	/**
	 * 获取调用接口凭证,并缓存access_token
	 * @return Ambigous <\app\common\lib\wechat\mixed, mixed, object>|boolean|mixed
	 */
	public function getToken(){
		if(empty($this->auth_refresh_token)){
			return parent::getToken();
		}else{
			return $this->getAuthToken($this->auth_refresh_token);
			
		}
	}
	/**
	 * 获取授权登陆二维码页面url
	 */
	public function getComponentLoginUrl($callback){
		$preAuthCode = $this->getPreAuthCode();
		if($preAuthCode){
			$callback = urlencode($callback);
			$loginUrl = "https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid={$this->component_appid}&pre_auth_code={$preAuthCode}&redirect_uri={$callback}";
			return 	$loginUrl;
		}else{
			return false;
		}
	}

	/**
	 * 使用授权码换取公众号的接口调用凭据和授权信息
	 * 回调$callback返回的auth_code
	 */	
	public function queryAuth($auth_code){
		//读取接口调用凭证
		$componentToken = $this->getComponentToken();
		if(!$componentToken){
			return false;		
		}
		
		//接口数据
		$api = [
			'url'	=> 	"https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token={$componentToken}",
			'post'	=>	[
							'component_appid'		=>$this->component_appid,
							'authorization_code' 	=>$auth_code,
						]
		];
		
		//接口调用
		$res = $this->http_post_json($api['url'],json_encode($api['post']));
		if ($res){
			$json = json_decode($res,true);
			if (empty($json) || isset($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			//返回数据
			return $json['authorization_info'];
		}
		return false;
	}
	
	/**
	 * 获取授权公众号信息
	 */
	public function getAuthInfo($auth_appid=''){
		//读取接口调用凭证
		$componentToken = $this->getComponentToken();
		if(!$componentToken){
			return false;		
		}
		
		//接口数据
		$api = [
			'url'	=> 	"https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token={$componentToken}",
			'post'	=>	[
							'component_appid' 			=>$this->component_appid,
							'authorizer_appid' 			=>empty($auth_appid)?$this->appid:$auth_appid,
						]
		];
		
		//接口调用
		$res = $this->http_post_json($api['url'],json_encode($api['post']));
		if ($res){
			$json = json_decode($res,true);
			if (empty($json) || isset($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			//返回数据
			return $json['authorizer_info'];
		}
		return false;
	}
	
	
	/**
     *通过authorizer_refresh_token来刷新公众号的接口调用凭据
	 */
	private function getAuthToken($auth_refresh_token){
		//缓存读取
		$cacheName = "token{this->appid}";
		$data = $this->getCache($cacheName);
		if($data && !$this->debug){
			return $data;
		}
		
		//读取接口调用凭证
		$componentToken = $this->getComponentToken();
		if(!$componentToken){
			return false;		
		}
		
		//接口数据
		$api = [
			'url'	=> 	"https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token={$componentToken}",
			'post'	=>	[
							'component_appid' 			=>$this->component_appid,
							'authorizer_appid' 			=>$this->appid,
							'authorizer_refresh_token' 	=>$auth_refresh_token,							
						]
		];
		//接口调用
		$res = $this->http_post_json($api['url'],json_encode($api['post']));
		if ($res){
			$json = json_decode($res,true);
			if (empty($json) || isset($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			//缓存数据
			$data = $json['authorizer_access_token'];
			$expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
			$this->setCache($cacheName,$data,$expire);
			return $data;
		}
		return false;		
	}
	
	/**
	 * 获取第三方平台component_access_token
	 * 第三方平台compoment_access_token是第三方平台的下文中接口的调用凭据，
	 * 也叫做令牌（component_access_token）。
	 * 每个令牌是存在有效期（2小时）的，且令牌的调用不是无限制的，
	 * 请第三方平台做好令牌的管理，在令牌快过期时（比如1小时50分）再进行刷新。
	 */
	protected  function getComponentToken(){
		//缓存读取
		$cacheName = "componenttoken{$this->component_appid}";
		$data = $this->getCache($cacheName);
		if($data && !$this->debug){
			return $data;
		}
		//接口数据
		$api = [
			'cache'	=> 	"componenttoken{$this->component_appid}",
			'url'	=> 	"https://api.weixin.qq.com/cgi-bin/component/api_component_token",
			'post' 	=> 	[
							'component_appid' =>$this->component_appid,
							'component_appsecret' =>$this->component_appsecret,
							'component_verify_ticket' =>$this->getComponentTicket(),
						]
		];
		//接口调用
		$res = $this->http_post_json($api['url'],json_encode($api['post']));
		if($res){
			$json = json_decode($res,true);
			if (empty($json) || isset($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				$this->log("getComponentToken:{$this->errMsg}", 'error');
				return false;
			}
			//缓存数据
			$data = $json['component_access_token'];
			$expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
			$this->setCache($cacheName,$data,$expire);
			$this->log("getComponentToken:{$data}", 'info');
			return $data;
		}
		return false;
	}
	
	/**
	 * 该API用于获取预授权码。预授权码用于公众号授权时的第三方平台方安全验证。
	 * @return Ambigous <\app\common\lib\com\wechat\mixed, mixed, object, unknown, boolean, \think\mixed, string>|boolean|Ambigous <boolean, unknown, mixed>
	 */
	private function getPreAuthCode(){
		//缓存读取
		$cacheName = "preauthcode{$this->component_appid}";
// 		$data = $this->getCache($cacheName);
// 		if($data && !$this->debug){
// 			return $data;
// 		}
		
		//读取接口调用凭证
		$componentToken = $this->getComponentToken();
		if(!$componentToken){
			return false;		
		}
		
		//接口数据
		$api = [
			'url'	=> 	"https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token={$componentToken}",
			'post' 	=> 	[
							'component_appid' =>$this->component_appid,
						]
		];
		//接口调用
		$res = $this->http_post_json($api['url'],json_encode($api['post']));
		if ($res){
			$json = json_decode($res,true);
			if (empty($json) || isset($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				$this->log("getPreAuthCode:{$this->errMsg}", 'error');
				return false;
			}
			$data = $json['pre_auth_code'];
			$expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
			$this->setCache($cacheName,$data,$expire);
			return $data;
		}
		return false;
	
	}
	
	/**
	 *微信服务器每隔10分钟会向第三方的消息接收地址推送一次component_verify_ticket，用于获取第三方平台接口调用凭据
	 */
	public function getComponentTicket(){
		return $this->getCache('WechatComTicket'.$this->component_appid);
	}
}

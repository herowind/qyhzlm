<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------

namespace com\weixin;

use think\Facade\Log;

class Weixin{
	protected $appid = '';
	protected $appsecret = '';
	protected $debug =  false;
	protected $errCode = 40001;
	protected $errMsg = "no access";
	
	
	public function __construct($options=[]){
		$this->appid 		= isset($options['appid'])?$options['appid']:'';
		$this->appsecret 	= isset($options['appsecret'])?$options['appsecret']:'';
	}
	
	/**
	 * 获取调用接口凭证,并缓存access_token
	 * @return Ambigous <\app\common\lib\wechat\mixed, mixed, object>|boolean|mixed
	 */
	public function getToken(){
		//缓存读取
		$cacheName = "token{this->appid}";
		$data = $this->getCache($cacheName);
		if($data){
			return $data;
		}
		
		//接口数据
		$api = [
			'url'	=> "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsecret}",
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
			//缓存数据
			$data = $json['access_token'];
			$expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
			$this->setCache($cacheName,$data,$expire);
			return $data;
		}
		return false;
	}

	/**
	 * 日志记录，可被重载。
	 * @param mixed $log 输入日志
	 * @return mixed
	 */
	protected function log($msg,$level){
		Log::record($msg,$level);
	}
	
	/**
	 * 数组输出xml字符
	 * @throws WxPayException
	 **/
	public function arrToXml($arr)
	{
		if(!is_array($arr) || count($arr) <= 0){
			return null;
		} 
		$xml = "<xml>";
		foreach ($arr as $key=>$val){
			if (is_numeric($val)){
				$xml.="<".$key.">".$val."</".$key.">";
			}else{
				$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
			}
		}
		$xml.="</xml>";
		return $xml;
	}
	
	/**
	 * xml输出数组
	 * @param string $xml
	 * @throws WxPayException
	 */
	public function xmlToArr($xml){
		if(!$xml){
			return null;
		}
		libxml_disable_entity_loader(true);
		$arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $arr;
	}
	
	/**
	 * 获取签名
	 * @param array $params 签名数组
	 * @param string $method 签名方法
	 * @return boolean|string 签名值
	 */
	public function getSignature($params,$method="sha1") {
		if (!function_exists($method)) return false;
		ksort($params);
		$signStr = '';
		foreach($params as $key => $value){
			if(strlen($signStr) == 0)
				$signStr .= strtolower($key) . "=" . $value;
			else
				$signStr .= "&" . strtolower($key) . "=" . $value;
		}
		$signStr = $method($signStr);
		return $signStr;
	}
	
	/**
	 * 生成随机字串
	 * @param number $length 长度，默认为16，最长为32字节
	 * @return string
	 */
	public function getNnonceStr($length=32){
		// 密码字符集，可任意添加你需要的字符
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for($i = 0; $i < $length; $i++)
		{
		$str .= $chars[mt_rand(0, strlen($chars) - 1)];
		}
		return $str;
	}
	
	/**
	 * 设置缓存，按需重载
	 * @param string $cachename
	 * @param mixed $value
	 * @param int $expired
	 * @return boolean
	 */
	protected function setCache($cachename,$value,$expired){
		$options['expire'] = $expired;
		cache($cachename,$value,$options);
	}
	
	/**
	 * 获取缓存，按需重载
	 * @param string $cachename
	 * @return mixed
	 */
	protected function getCache($cachename){
		$cache = cache($cachename);
		return $cache;
	}
	
	/**
	 * 清除缓存，按需重载
	 * @param string $cachename
	 * @return boolean
	 */
	protected function removeCache($cachename){
		cache($cachename,null);
	}
	
	/**
	 * GET 请求
	 * @param string $url
	 */
	protected function http_get($url){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}
	
	/**
	 * ssl不带证书发包
	 * @param unknown_type $url
	 * @param unknown_type $params
	 * @param unknown_type $second
	 * @param unknown_type $aHeader
	 * @return mixed|boolean
	 */
	public function http_post_json($url, $params, $second=30,$aHeader=array()){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);//设置超时
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
		//这里设置代理，如果有的话
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		//post提交方式:JSON参数
		curl_setopt($ch,CURLOPT_POST, TRUE);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$params);
		curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$data = curl_exec($ch);
		if($data){
			curl_close($ch);
			return $data;
		}
		else {
			$error = curl_errno($ch);
			curl_close($ch);
			return false;
		}
	}
	
	/**
	 * POST 请求
	 * @param string $url
	 * @param array $param
	 * @param boolean $post_file 是否文件上传
	 * @return string content
	 */
	protected function http_post($url,$param,$post_file=false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		if (is_string($param) || $post_file) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			foreach($param as $key=>$val){
				$aPOST[] = $key."=".urlencode($val);
			}
			$strPOST =  join("&", $aPOST);
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

	//get方法
	public function __get($property_name){
		if(isset($this->$property_name)){
			return $this->$property_name;
		}else{
			return null;
		}
	}
	//set方法
	public function __set($property_name, $value){
		$this->$property_name = $value;
	}	
	
}

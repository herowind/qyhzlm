<?php
namespace com\weixin\pay;
use think\Facade\Log;

/**
 * 
 * 数据对象基础类，该类中定义数据类最基本的行为，包括：
 * 计算/设置/获取签名、输出xml格式的参数、从xml读取数据对象等
 * @author oliver 2244115959@qq.com
 *
 */
class WxPay{
	protected $appid 		= '';
	protected $mch_id 		= '';
	protected $mch_secret 	= '';
	protected $debug 		= false;
	protected $errcode 		= 40001;
	protected $errmsg 		= 'no access';
	protected $cert_path 	= '';
	protected $key_path 	= '';
	
	/**
	 * 初试化基本参数
	 * @param unknown_type $options
	 */
	public function __construct($options) {
		$this->appid 		= isset($options['appid'])?$options['appid']:'';
		$this->mch_id 		= isset($options['mch_id'])?$options['mch_id']:'';
		$this->mch_secret 	= isset($options['mch_secret'])?$options['mch_secret']:'';
		$this->debug 		= isset($options['debug'])?$options['debug']:false;
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
	public function getSignature($params,$method="md5") {
		ksort($params);
		$signStr = '';
		foreach($params as $key => $value){
			if($key == 'sign'){
				continue;
			}
			if(strlen($signStr) == 0)
				$signStr .= $key . "=" . $value;
			else
				$signStr .= "&" . $key . "=" . $value;
		}
		$signStr .= "&key=".$this->mch_secret;
		$signStr = $method($signStr);
		return strtoupper($signStr);
	}
	
	/**
	 * 生成随机字串
	 * @param number $length 长度，默认为16，最长为32字节
	 * @return string
	 */
	public function getNonceStr($length=32){
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
	 * 将xml转为array
	 * @param string $xml
	 * @throws WxPayException
	 */
	public function processRes($xml){
		$data = $this->xmlToArr($xml);
		if($data['return_code'] != 'SUCCESS'){
			return $data;
		}
		//验证签名
		$sign = $this->getSignature($data);
		if($sign == $data['sign']){
			return $data;
		}else{
			throw new WxPayException("签名错误！");
		}
	}
	
	/**
	 * 以post方式提交xml到对应的接口url
	 *
	 * @param string $xml  需要post的xml数据
	 * @param string $url  url
	 * @param bool $useCert 是否需要证书，默认不需要
	 * @param int $second   url执行超时时间，默认30s
	 * @throws WxPayException
	 */
	public function http_post_xml($url,$xml,$usecert = false,$second = 30){
		if(is_array($xml)){
			$xml = $this->arrToXml($xml);
		}
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);//严格校验
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	
		if($usecert == true){
			//设置证书
			//使用证书：cert 与 key 分别属于两个.pem文件
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT, $this->cert_path);
			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY, $this->key_path);
		}
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		} else {
			$error = curl_errno($ch);
			curl_close($ch);
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
	
}
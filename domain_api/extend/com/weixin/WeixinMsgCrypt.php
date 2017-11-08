<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------

namespace com\weixin;

use com\weixin\utils\PKCS7Encoder;

class WeixinMsgCrypt{ 

	
	private $token;
	private $encodingAesKey;
	private $appId;
	private $cryptKey;

	/**
	 * 构造函数
	 * @param $token string 公众平台上，开发者设置的token
	 * @param $encodingAesKey string 公众平台上，开发者设置的EncodingAESKey
	 * @param $appId string 公众平台的appId
	 */
	public function __construct($token, $encodingAesKey, $appId)
	{
		$this->token = $token;
		$this->encodingAesKey = $encodingAesKey;
		$this->appId = $appId;
		$this->cryptKey = base64_decode($encodingAesKey . "=");
	}

	/**
	 * 将公众平台回复用户的消息加密打包.
	 * <ol>
	 *    <li>对要发送的消息进行AES-CBC加密</li>
	 *    <li>生成安全签名</li>
	 *    <li>将消息密文和安全签名打包成xml格式</li>
	 * </ol>
	 *
	 * @param $replyMsg string 公众平台待回复用户的消息，xml格式的字符串
	 * @param $timeStamp string 时间戳，可以自己生成，也可以用URL参数的timestamp
	 * @param $nonce string 随机串，可以自己生成，也可以用URL参数的nonce
	 * @param &$encryptMsg string 加密后的可以直接回复用户的密文，包括msg_signature, timestamp, nonce, encrypt的xml格式的字符串,
	 *                      当return返回0时有效
	 *
	 * @return int 成功0，失败返回对应的错误码
	 */
	public function encryptMsg($replyMsg, $timeStamp, $nonce, &$encryptMsg){
		//加密
		$encryptData = $this->encrypt($replyMsg, $this->appId);
		if ($encryptData['code'] !== 1) {
			return $encryptData;
		}

		if ($timeStamp == null) {
			$timeStamp = time();
		}
		//生成安全签名
		$signData = $this->getSHA1($this->token, $timeStamp, $nonce, $encryptData['data']);
		if ($signData['code'] !== 1) {
			return $signData;
		}

		//生成发送的xml
		$encryptMsg = $this->generateXml($encryptData['data'], $signData['data'], $timeStamp, $nonce);
		return ErrorCode::$OK;
	}


	/**
	 * 检验消息的真实性，并且获取解密后的明文.
	 * <ol>
	 *    <li>利用收到的密文生成安全签名，进行签名验证</li>
	 *    <li>若验证通过，则提取xml中的加密消息</li>
	 *    <li>对消息进行解密</li>
	 * </ol>
	 *
	 * @param $msgSignature string 签名串，对应URL参数的msg_signature
	 * @param $timestamp string 时间戳 对应URL参数的timestamp
	 * @param $nonce string 随机串，对应URL参数的nonce
	 * @param $postData string 密文，对应POST请求的数据
	 * @param &$msg string 解密后的原文，当return返回0时有效
	 *
	 * @return int 成功0，失败返回对应的错误码
	 */
	public function decryptMsg($msgSignature, $timestamp = null, $nonce, $postData, &$msg){
		if (strlen($this->encodingAesKey) != 43) {
			return ErrorCode::$IllegalAesKey;
		}
		
		$msgData = $this->extractXml($postData);
		if ($msgData['code'] !== 1) {
			return $msgData;
		}

		if ($timestamp == null) {
			$timestamp = time();
		}

		$encrypt = $msgData['data']['encrypt'];
		$tousername = $msgData['data']['tousername'];

		//验证安全签名
		$signData = $this->getSHA1($this->token, $timestamp, $nonce, $encrypt);
		if ($signData['code'] !== 1) {
			return $signData;
		}
		
		if ($signData['data'] != $msgSignature) {
			return ErrorCode::$ValidateSignatureError;
		}

		$decryptData = $this->decrypt($encrypt, $this->appId);
		if ($decryptData['code'] !== 1) {
			return $decryptData;
		}
		$msg = $decryptData['data'];

		return ErrorCode::$OK;
	}
	
	public function decryptTicket($msgSignature, $timestamp = null, $nonce, $postData, &$msg){
		if (strlen($this->encodingAesKey) != 43) {
			return ErrorCode::$IllegalAesKey;
		}
	
		$msgData = $this->extractXml($postData);
		if ($msgData['code'] !== 1) {
			return $msgData;
		}
	
		if ($timestamp == null) {
			$timestamp = time();
		}
	
		$encrypt = $msgData['data']['encrypt'];
		$appid   = $msgData['data']['appid'];
	
		//验证安全签名
		$signData = $this->getSHA1($this->token, $timestamp, $nonce, $encrypt);
		if ($signData['code'] !== 1) {
			return $signData;
		}
	
		if ($signData['data'] != $msgSignature) {
			return ErrorCode::$ValidateSignatureError;
		}
	
		$decryptData = $this->decrypt($encrypt, $this->appId);
		if ($decryptData['code'] !== 1) {
			return $decryptData;
		}
		$msg = $decryptData['data'];
	
		return ErrorCode::$OK;
	}	
	
	/**
	 * 用SHA1算法生成安全签名
	 * @param string $token 票据
	 * @param string $timestamp 时间戳
	 * @param string $nonce 随机字符串
	 * @param string $encrypt 密文消息
	 */
	public function getSHA1($token, $timestamp, $nonce, $encrypt_msg)
	{
		//排序
		try {
			$array = array($encrypt_msg, $token, $timestamp, $nonce);
			sort($array, SORT_STRING);
			$str = implode($array);
			return  array_merge(ErrorCode::$OK,['data'=>sha1($str)]);
		} catch (\Exception $e) {
			//print $e . "\n";
			return ErrorCode::$ComputeSignatureError;
		}
	}
	
	/**
	 * 提取出xml数据包中的加密消息
	 * @param string $xmltext 待提取的xml字符串
	 * @return string 提取出的加密消息字符串
	 */
	public function extractXml($xmltext){
		try {
			$xml = new \DOMDocument();
			$xml->loadXML($xmltext);
			$array_e = $xml->getElementsByTagName('Encrypt');
			$array_a = $xml->getElementsByTagName('ToUserName');
			$encrypt = $array_e->item(0)->nodeValue;
			$tousername = $array_a->item(0)->nodeValue;
			return array_merge(ErrorCode::$OK,['data'=>['encrypt'=>$encrypt,'tousername'=>$tousername]]);
		} catch (\Exception $e) {
			//print $e . "\n";
			return ErrorCode::$ParseXmlError;
		}
	}
	
	/**
	 * 生成xml消息
	 * @param string $encrypt 加密后的消息密文
	 * @param string $signature 安全签名
	 * @param string $timestamp 时间戳
	 * @param string $nonce 随机字符串
	 */
	public function generateXml($encrypt, $signature, $timestamp, $nonce){
		$format = "<xml>
<Encrypt><![CDATA[%s]]></Encrypt>
<MsgSignature><![CDATA[%s]]></MsgSignature>
<TimeStamp>%s</TimeStamp>
<Nonce><![CDATA[%s]]></Nonce>
</xml>";
		return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
	}
	
	
	/**
	 * 对明文进行加密
	 * @param string $text 需要加密的明文
	 * @return string 加密后的密文
	 */
	public function encrypt($text, $appid){
		try {
			//获得16位随机字符串，填充到明文之前
			$random = $this->getRandomStr();
			$text = $random . pack("N", strlen($text)) . $text . $appid;
			// 网络字节序
			$size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			$iv = substr($this->cryptKey, 0, 16);
			//使用自定义的填充方式对明文进行补位填充
			$pkc_encoder = new PKCS7Encoder;
			$text = $pkc_encoder->encode($text);
			mcrypt_generic_init($module, $this->cryptKey, $iv);
			//加密
			$encrypted = mcrypt_generic($module, $text);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);
	
			//print(base64_encode($encrypted));
			//使用BASE64对加密后的字符串进行编码
			$resData = self::$OK;
			$resData['data'] = base64_encode($encrypted);
			return $resData;
		} catch (\Exception $e) {
			//print $e;
			return self::$ErrorEncryptAES;
		}
	}
	
	/**
	 * 对密文进行解密
	 * @param string $encrypted 需要解密的密文
	 * @return string 解密得到的明文
	 */
	public function decrypt($encrypted, $appid){
		try {
			//使用BASE64对需要解密的字符串进行解码
			$ciphertext_dec = base64_decode($encrypted);
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			$iv = substr($this->cryptKey, 0, 16);
			mcrypt_generic_init($module, $this->cryptKey, $iv);
	
			//解密
			$decrypted = mdecrypt_generic($module, $ciphertext_dec);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);
		} catch (\Exception $e) {
			return self::$ErrorDecryptAES;
		}
		try {
			//去除补位字符
			$pkc_encoder = new PKCS7Encoder;
			$result = $pkc_encoder->decode($decrypted);
			//去除16位随机字符串,网络字节序和AppId
			if (strlen($result) < 16){
				return "";
			}
			$content = substr($result, 16, strlen($result));
			$len_list = unpack("N", substr($content, 0, 4));
			$xml_len = $len_list[1];
			$xml_content = substr($content, 4, $xml_len);
			$from_appid = substr($content, $xml_len + 4);
		} catch (\Exception $e) {
			//print $e;
			return ErrorCode::$IllegalBuffer;
		}
		if ($from_appid != $appid){
			return ErrorCode::$ValidateAppidError;
		}
			
		return array_merge(ErrorCode::$OK,['data'=>$xml_content]);
	
	}
	
	/**
	 * 随机生成16位字符串
	 * @return string 生成的字符串
	 */
	function getRandomStr(){
		$str = "";
		$str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($str_pol) - 1;
		for ($i = 0; $i < 16; $i++) {
			$str .= $str_pol[mt_rand(0, $max)];
		}
		return $str;
	}
}

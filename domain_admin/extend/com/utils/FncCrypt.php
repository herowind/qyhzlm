<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------
namespace com\utils;

/**
 * 通用函数库
 * @author oliver
 *
 */
class FncCrypt{
	static $data_auth_key = '1q2w3e4r5t6y!Q@W#E$R%T';
	
	/**
	 * 系统加密方法
	 * @param string $data 要加密的字符串
	 * @param string $key  加密密钥
	 * @param int $expire  过期时间 单位 秒
	 * @return string
	 */
	public static function encrypt($data, $key = '', $expire = 0) {
		$key  = md5(empty($key) ? '1q2w3e4r5t6y!Q@W#E$R%T' : $key);
		$data = base64_encode($data);
		$x    = 0;
		$len  = strlen($data);
		$l    = strlen($key);
		$char = '';
	
		for ($i = 0; $i < $len; $i++) {
			if ($x == $l) $x = 0;
			$char .= substr($key, $x, 1);
			$x++;
		}
	
		$str = sprintf('%010d', $expire ? $expire + time():0);
	
		for ($i = 0; $i < $len; $i++) {
			$str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
		}
		return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
	}
	
	/**
	 * 系统解密方法
	 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
	 * @param  string $key  加密密钥
	 * @return string
	 */
	public static function decrypt($data, $key = ''){
		$key    = md5(empty($key) ? '1q2w3e4r5t6y!Q@W#E$R%T' : $key);
		$data   = str_replace(array('-','_'),array('+','/'),$data);
		$mod4   = strlen($data) % 4;
		if ($mod4) {
			$data .= substr('====', $mod4);
		}
		$data   = base64_decode($data);
		$expire = substr($data,0,10);
		$data   = substr($data,10);
	
		if($expire > 0 && $expire < time()) {
			return '';
		}
		$x      = 0;
		$len    = strlen($data);
		$l      = strlen($key);
		$char   = $str = '';
	
		for ($i = 0; $i < $len; $i++) {
			if ($x == $l) $x = 0;
			$char .= substr($key, $x, 1);
			$x++;
		}
	
		for ($i = 0; $i < $len; $i++) {
			if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
				$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
			}else{
				$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
			}
		}
		return base64_decode($str);
	}
	
	/**
	 * 数据签名认证
	 * @param  array  $data 被认证的数据
	 * @return string       签名
	 */
	public static function dataAuthSign($data) {
		//数据类型检测
		if(!is_array($data)){
			$data = (array)$data;
		}
		ksort($data); //排序
		$code = http_build_query($data); //url编码并生成query字符串
		$sign = sha1($code); //生成签名
		return $sign;
	}
}
<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------

namespace com\weixin;
/**
 * error code 说明.
 * <ul>
 *    <li>-40001: 签名验证错误</li>
 *    <li>-40002: xml解析失败</li>
 *    <li>-40003: sha加密生成签名失败</li>
 *    <li>-40004: encodingAesKey 非法</li>
 *    <li>-40005: appid 校验错误</li>
 *    <li>-40006: aes 加密失败</li>
 *    <li>-40007: aes 解密失败</li>
 *    <li>-40008: 解密后得到的buffer非法</li>
 *    <li>-40009: base64加密失败</li>
 *    <li>-40010: base64解密失败</li>
 *    <li>-40011: 生成xml失败</li>
 * </ul>
 */
class ErrorCode{
	public static $OK 						= ['code'=>1,'msg'=>'操作成功'];
	//加密模块
	public static $ValidateSignatureError 	= ['code'=>-40001,'msg'=>'签名验证错误'];
	public static $ParseXmlError 			= ['code'=>-40002,'msg'=>'xml解析失败'];
	public static $ComputeSignatureError 	= ['code'=>-40003,'msg'=>'sha加密生成签名失败'];
	public static $IllegalAesKey			= ['code'=>-40004,'msg'=>'encodingAesKey 非法'];
	public static $ValidateAppidError 		= ['code'=>-40005,'msg'=>'appid 校验错误'];
	public static $EncryptAESError 			= ['code'=>-40006,'msg'=>'aes 加密失败'];
	public static $DecryptAESError			= ['code'=>-40007,'msg'=>'aes 解密失败'];
	public static $IllegalBuffer 			= ['code'=>-40008,'msg'=>'解密后得到的buffer非法'];
	public static $EncodeBase64Error		= ['code'=>-40009,'msg'=>'base64加密失败'];
	public static $DecodeBase64Error		= ['code'=>-40010,'msg'=>'base64解密失败'];
	public static $GenReturnXmlError 		= ['code'=>-40011,'msg'=>'生成xml失败'];
}

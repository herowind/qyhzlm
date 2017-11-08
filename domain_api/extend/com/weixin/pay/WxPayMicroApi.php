<?php
namespace com\weixin\pay;

/**
 * 
 * 提交被扫支付API
 * 收银员使用扫码设备读取微信用户刷卡授权码以后，二维码或条码信息传送至商户收银台，
 * 由商户收银台或者商户后台调用该接口发起支付。
 * WxPayWxPayMicroPay中body、out_trade_no、total_fee、auth_code参数必填
 * appid、mchid、spbill_create_ip、nonce_str不需要填入
 * @param database\WxPayMicroPay $inputObj
 * @param int $timeOut
 * @return array
 * @throws WxPayException
 */
class WxPayMicroApi extends WxPayApi{

}
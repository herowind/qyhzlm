<?php
namespace com\weixin\pay;

use think\Log;

/**
 * 
 * JSAPI支付实现类
 * 该类实现了从微信公众平台获取code、通过code获取openid和access_token、
 * 生成jsapi支付js接口所需的参数、生成获取共享收货地址所需的参数
 * 
 * 该类是微信支付提供的样例程序，商户可根据自己的需求修改，或者使用lib中的api自行开发
 * 
 * @author widy
 *
 */
class WxPayJsApi extends WxPayApi{
	/**
	 *
	 * 获取jsapi支付的参数
	 * @param array $UnifiedOrderResult 统一支付接口返回的数据
	 * @throws WxPayException
	 *
	 * @return json数据，可直接填入js函数作为参数
	 */
	public function getJsApiParams($inputObj){
		Log::record($inputObj);
		$unifiedOrderRes = $this->unifiedOrder($inputObj);
		Log::record($unifiedOrderRes);
		if($unifiedOrderRes['return_code'] == 'SUCCESS' && $unifiedOrderRes['result_code'] == 'SUCCESS'){
			$params = [
						'appId'			=> $unifiedOrderRes['appid'],
						'timeStamp' 	=> time().'',
						'nonceStr'		=> $this->wxPay->getNonceStr(),
						'package'		=> "prepay_id={$unifiedOrderRes['prepay_id']}",
						'signType'		=> 'MD5'
					];
			$params['paySign'] = $this->wxPay->getSignature($params);
			return $params;
		}else{
			Log::record($unifiedOrderRes['err_code_des']);
			Log::record($unifiedOrderRes['err_code']);
			$this->errorcode = $unifiedOrderRes['err_code'];
			$this->errormsg = $unifiedOrderRes['err_code_des'];
			return false;
		}
		

	}
}
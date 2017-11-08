<?php
namespace com\weixin\pay;
use think\Facade\Log;

/**
 * 
 * 接口访问类，包含所有微信支付API列表的封装，类中方法为static方法，
 * 每个接口有默认超时时间（除提交被扫支付为10s，上报超时时间为1s外，其他均为6s）
 * @author oliver 2244115959@qq.com
 *
 */
class WxPayApi{
	protected $wxPay;
	protected $errorcode;
	protected $errormsg;
	
	//构造函数，初始化的时候最先执行
	public function __construct($options=[]) {
		$this->wxPay = new WxPay($options);
	}
	
	/**
	 *
	 * 统一下单，WxPayUnifiedOrder中out_trade_no、body、total_fee、trade_type必填
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayUnifiedOrder $inputObj
	 * @param int $timeOut
	 * @throws WxPayException
	 * @return 成功时返回，其他抛异常
	 */
	protected function unifiedOrder($inputObj, $timeOut = 6){
		$url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		//检测必填参数
		if(!$inputObj->isOut_trade_noSet()) {
			throw new WxPayException("缺少统一支付接口必填参数out_trade_no！");
		}else if(!$inputObj->isBodySet()){
			throw new WxPayException("缺少统一支付接口必填参数body！");
		}else if(!$inputObj->isTotal_feeSet()) {
			throw new WxPayException("缺少统一支付接口必填参数total_fee！");
		}else if(!$inputObj->isTrade_typeSet()) {
			throw new WxPayException("缺少统一支付接口必填参数trade_type！");
		}
	
		//关联参数
		if($inputObj->trade_type == "JSAPI" && !$inputObj->isOpenidSet()){
			throw new WxPayException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
		}else if($inputObj->trade_type == "NATIVE" && !$inputObj->isProduct_idSet()){
			throw new WxPayException("统一支付接口中，缺少必填参数product_id！trade_type为JSAPI时，product_id为必填参数！");
		}
	
		//异步通知url未设置，则使用配置文件中的url
		if(!$inputObj->isNotify_urlSet()){
			throw new WxPayException("统一支付接口中，缺少必填参数notify_url！");
		}
		
		$inputObj->appid = $this->wxPay->appid; //公众账号ID
		$inputObj->mch_id = $this->wxPay->mch_id;//商户号
		$inputObj->spbill_create_ip = $_SERVER['REMOTE_ADDR'];//终端ip
		$inputObj->nonce_str = $this->wxPay->getNonceStr();
		$inputObj->time_start = date('YmdHis', time());
		$inputObj->time_expire = date('YmdHis', time() + 600);
	    Log::record($this->wxPay);
		//签名
		$inputObj->sign = $this->wxPay->getSignature($inputObj->fetchData());
		$resXml = $this->wxPay->http_post_xml($url, $inputObj->fetchData());
		$unifiedOrderRes = $this->wxPay->processRes($resXml);	
		return $unifiedOrderRes;
	}
	
	/**
	 *
	 * 获取jsapi支付的参数
	 * @param array $UnifiedOrderResult 统一支付接口返回的数据
	 * @throws WxPayException
	 *
	 * @return json数据，可直接填入js函数作为参数
	 */
	public function micropay($inputObj, $timeOut = 10){
		$url = "https://api.mch.weixin.qq.com/pay/micropay";
		//检测必填参数
		if (!$inputObj->isBodySet()) {
			throw new WxPayException("提交被扫支付API接口中，缺少必填参数body！");
		} else if (!$inputObj->isOut_trade_noSet()) {
			throw new WxPayException("提交被扫支付API接口中，缺少必填参数out_trade_no！");
		} else if (!$inputObj->isTotal_feeSet()) {
			throw new WxPayException("提交被扫支付API接口中，缺少必填参数total_fee！");
		} else if (!$inputObj->isAuth_codeSet()) {
			throw new WxPayException("提交被扫支付API接口中，缺少必填参数auth_code！");
		}
		
		$inputObj->appid = $this->wxPay->appid; //公众账号ID
		$inputObj->mch_id = $this->wxPay->mch_id;//商户号
		$inputObj->spbill_create_ip = $_SERVER['REMOTE_ADDR'];//终端ip
		$inputObj->nonce_str = $this->wxPay->getNonceStr();
		$inputObj->sign = $this->wxPay->getSignature($inputObj->fetchData());
		$resXml = $this->wxPay->http_post_xml($url, $inputObj->fetchData());
	
		$unifiedOrderRes = $this->wxPay->processRes($resXml);
		//$startTimeStamp = self::getMillisecond();//请求开始时间
		//self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		return $unifiedOrderRes;
	}
	
	
	public function getErrorCode(){
		return $this->errorcode;
	}
	public function getErrorMsg(){
		return $this->errormsg;
	}
}
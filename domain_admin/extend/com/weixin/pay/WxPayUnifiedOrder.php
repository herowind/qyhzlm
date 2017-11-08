<?php
/**
* 2015-06-29 修复签名问题
**/
namespace com\weixin\pay;
/**
 *
 * 统一下单输入对象
 * @author widyhu
 *
 */
class WxPayUnifiedOrder{
	private $data = [];
	private $appid;
	private $mch_id;
	private $body;
	private $detail;
	private $openid;
	private $product_id;
	private $trade_type;
	private $notify_url;
	private $goods_tag;
	private $time_expire;
	private $time_start;
	private $attach;
	private $device_info;
	private $out_trade_no;
	private $fee_type;
	private $total_fee;
	private $spbill_create_ip;
	private $nonce_str;
	private $auth_code;
	private $sign;
	
	
	
    /**
     * 判断微信分配的公众账号ID是否存在
     * @return true 或 false
     **/
    public function isAppidSet()
    {
        return array_key_exists('appid', $this->data);
    }

    /**
     * 判断微信支付分配的商户号是否存在
     * @return true 或 false
     **/
    public function isMch_idSet()
    {
        return array_key_exists('mch_id', $this->data);
    }

    /**
     * 判断微信支付分配的终端设备号，商户自定义是否存在
     * @return true 或 false
     **/
    public function isDevice_infoSet()
    {
        return array_key_exists('device_info', $this->data);
    }

    /**
     * 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
     * @return true 或 false
     **/
    public function isNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->data);
    }


    /**
     * 判断商品或支付单简要描述是否存在
     * @return true 或 false
     **/
    public function isBodySet()
    {
        return array_key_exists('body', $this->data);
    }


    /**
     * 判断商品名称明细列表是否存在
     * @return true 或 false
     **/
    public function isDetailSet()
    {
        return array_key_exists('detail', $this->data);
    }

    /**
     * 判断附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据是否存在
     * @return true 或 false
     **/
    public function isAttachSet()
    {
        return array_key_exists('attach', $this->data);
    }

    /**
     * 判断商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号是否存在
     * @return true 或 false
     **/
    public function isOut_trade_noSet()
    {
        return array_key_exists('out_trade_no', $this->data);
    }

    /**
     * 判断符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型是否存在
     * @return true 或 false
     **/
    public function isFee_typeSet()
    {
        return array_key_exists('fee_type', $this->data);
    }
    /**
     * 判断订单总金额，只能为整数，详见支付金额是否存在
     * @return true 或 false
     **/
    public function isTotal_feeSet()
    {
        return array_key_exists('total_fee', $this->data);
    }

    /**
     * 判断APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。是否存在
     * @return true 或 false
     **/
    public function isSpbill_create_ipSet()
    {
        return array_key_exists('spbill_create_ip', $this->data);
    }

    /**
     * 判断订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则是否存在
     * @return true 或 false
     **/
    public function isTime_startSet()
    {
        return array_key_exists('time_start', $this->data);
    }

    /**
     * 判断订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则是否存在
     * @return true 或 false
     **/
    public function isTime_expireSet()
    {
        return array_key_exists('time_expire', $this->data);
    }


    /**
     * 判断商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠是否存在
     * @return true 或 false
     **/
    public function isGoods_tagSet()
    {
        return array_key_exists('goods_tag', $this->data);
    }

    /**
     * 判断接收微信支付异步通知回调地址是否存在
     * @return true 或 false
     **/
    public function isNotify_urlSet()
    {
        return array_key_exists('notify_url', $this->data);
    }


    /**
     * 判断取值如下：JSAPI，NATIVE，APP，详细说明见参数规定是否存在
     * @return true 或 false
     **/
    public function isTrade_typeSet()
    {
        return array_key_exists('trade_type', $this->data);
    }

    /**
     * 判断trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。是否存在
     * @return true 或 false
     **/
    public function isProduct_idSet()
    {
        return array_key_exists('product_id', $this->data);
    }

    /**
     * 判断trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。 是否存在
     * @return true 或 false
     **/
    public function isOpenidSet(){
        return array_key_exists('openid', $this->data);
    }
    
    /**
     * 判断授权代码
     * @return true 或 false
     **/
    public function isAuth_codeSet()
    {
    	return array_key_exists('auth_code', $this->data);
    }
    
    public function fetchData(){
    	return $this->data;
    }
    
    //get方法
    public function __get($property_name){
    	if(isset($this->data[$property_name])){
    		return $this->data[$property_name];
    	}else{
    		return null;
    	}
    }
    
    //set方法
    public function __set($property_name, $value){
    	//if(isset($this->$property_name)){
    		$this->data[$property_name] = $value;
    	//}else{
    	//	throw new WxPayException("参数不存在！");
    	//}
    	
    }
}


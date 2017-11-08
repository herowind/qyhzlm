<?php
// +----------------------------------------------------------------------
// | 联盟管理平台
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2020 http://www.qyhzlm.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( 商业版权，禁止传播，违者必究  )
// +----------------------------------------------------------------------
// | Author: oliver <2244115959@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 微信支付
// +----------------------------------------------------------------------

namespace app\alliance_api\controller;

use com\weixin\pay\WxPayJsApi;
use com\weixin\pay\WxPayUnifiedOrder;
use app\common\controller\AppController;
use app\alliance_api\model\AllianceOrder;
use think\facade\Log;

class Grouppay extends AppController{
    // 初始化
    public function _initialize(){
        parent::_initialize();
        $this->checkLogin();
    }
	public function jsapipay(){
		$orderid = $this->request->param('orderid');
		$order = AllianceOrder::get($orderid);
		
		// 订单不存在
		if(empty($order)){
			return ['code'=>0,'msg'=>'网络不给力，请重新尝试'];
		}
		// 订单已支付
		if($order['ispay'] == 1){
			return ['code'=>0,'msg'=>'已经支付成功, 不需要重复支付'];
		}
		
		//省略验签
		//获得微信支付平台账号
		$wxOptions = config('wxapp.pay_setting');
		Log::record('pay_setting:'.$wxOptions);
		//授权获得openid
		$openid = $this->user['openid'];	
		//统一下单inputObj
		$unifiedOrder = new WxPayUnifiedOrder();
		$unifiedOrder->body 		= $order['ordertypetext']?$order['ordertypetext']:'服务费';
		$unifiedOrder->detail 		= '服务费';
		$unifiedOrder->out_trade_no = $order['orderno'];
		$unifiedOrder->total_fee 	= $order['payfee'] * 100;
		$unifiedOrder->attach 		= $order['uid'];
		$unifiedOrder->openid 		= $openid;
		$unifiedOrder->notify_url 	= 'http://'.$_SERVER['HTTP_HOST']."/index.php/alliance_api/Grouppaynotify/notify.html";
		$unifiedOrder->trade_type   = 'JSAPI';
		
		//微信支付接口调用[jsapi方式]
		$wxPayApi = new WxPayJsApi($wxOptions);
		$jsApiParameters = $wxPayApi->getJsApiParams($unifiedOrder);
		if($jsApiParameters){		
			return ['code'=>1,'msg'=>'开始微信支付','data'=>$jsApiParameters];
		}else{
			return ['code'=>0,'msg'=>$wxPayApi->getErrorMsg().",如有疑问请微信客服"];
		}		
	}
}

?>
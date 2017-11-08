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
// | 支付回调
// +----------------------------------------------------------------------

namespace app\alliance_api\controller;

use app\alliance_api\model\AllianceOrder;
use app\alliance_api\model\AlliancePay;
use think\facade\Log;
use app\alliance_api\model\AllianceGroup;
use app\core_api\model\User;

class Grouppaynotify{
    
    public function notify(){
        $order = $this->process();
        if($order){
            switch($order['ordertype']){
                case 'groupjoin':
                    $this->groupjoin($order);
                    break;
                case 'reward':
                    $this->reward($order);
                    break;
                default:
                    exit();
            }
        }
        exit();
    }
    
    /**
     * 入会费回调
     */
	private function groupjoin($order){
        $group = AllianceGroup::field('id,totalfee')->find($order['gid']);
        if($group){
            $group->totalfee = ['exp',"totalfee+{$order['payfee']}"];
            //更新群资金
            $group->save();
        } 
	}
	
	/**
	 * 打赏回调
	 */
	private function reward($order){
        $user = User::field('id,totalfee')->find($order['touid']);
        if($user){
            //更新被打赏人金额
            $user->totalfee = ['exp',"totalfee+{$order['payfee']}"];
            $user->save();
        }
	}
	
	/**
	 * 微信支付通知:返回data数据
	 * appid:wx137fe094a22017d0
	 * attach:1447482390753
	 * bank_type:CFT
	 * cash_fee:100
	 * fee_type:CNY
	 * is_subscribe:Y
	 * mch_id:1232803702
	 * nonce_str:6agm7tffyj2qtxhopu5hvp97bhexsyj2
	 * openid:oj16Ys956M5EJ1XbF5WX-Ut7C5TY
	 * out_trade_no:1447482390753
	 * result_code:SUCCESS
	 * return_code:SUCCESS
	 * sign:CE6A7E4024D6438D6C7E5C6961A4D8AD
	 * time_end:20151114142639
	 * total_fee:100
	 * trade_type:JSAPI
	 * transaction_id:1006160262201511141598831310
	 */
	private function process(){
	    //$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
	    $xml = file_get_contents("php://input");
	    Log::record($xml,'info');
	    $data = $this->xmlToArr($xml);
	    Log::record($xml,'info');
	    if (empty($data)) {
	        Log::record("订单处理失败：".$xml);
	        return false;
	    }
	    if ($data['result_code'] != 'SUCCESS' || $data['return_code'] != 'SUCCESS') {
	        Log::record("订单处理失败：".$xml);
	        return false;
	    }
	    //查询订单
	    $order = AllianceOrder::get(['orderno'=>$data['out_trade_no']]);
	    if(empty($order)){
	        Log::record("WeixinPay:trade_no:".$data['out_trade_no']."订单不存在！");
	        return false;
	    }
	    if($order->ispay == 1){
	        Log::record("WeixinPay:trade_no:".$data['out_trade_no']."订单处理完毕！");
	        return false;
	    }
	
	    //设置订单支付状态
	    $order->ispay = 1;
	    $order->save();
	
	    //记录支付流水
	    $trade['transactionid'] = $data['transaction_id'];
	    $trade['orderid'] = $order->id;
	    $trade['uid'] = $data['attach'];
	    $trade['mchid'] = $data['mch_id'];
	    $trade['openid'] = $data['openid'];
	    $trade['payfee'] = $data['cash_fee']/100;
	    $trade['paystatus'] = 1;
	    AlliancePay::create($trade);
	    return $order;
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
}

?>
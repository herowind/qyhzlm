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
// | 用户信息
// +----------------------------------------------------------------------

namespace app\core_api\controller;

use app\common\controller\AppController;
use app\core_api\model\User;
use app\core_api\model\UserWithdraw;
use think\Validate;

class Userwallet extends AppController{
	//初始化
	public function _initialize(){
		parent::_initialize();
		parent::checkLogin();
	}
	
	public function detail(){
	    $user = User::field('id,realname,mobile,totalfee')->find($this->user['id']);
	    return ['code'=>1,'msg'=>'操作成功','data'=>$user];
	}
	
	/**
	 * 用户提现
	 */
	public function withdraw(){
	    $amount = $this->request->param('amount');
	    //验证金额正确
	    $validate = Validate::make([
	        'amount'  => 'require|float'
	    ]);
	    if (!$validate->check(['amount'=>$amount])) {
	        return ['code'=>0,'msg'=>'金额输入不正确'];
	    }
	    
	    $user = User::field('id,openid,realname,mobile,totalfee')->find($this->user['id']);
	    $user->totalfee = bcsub($user->totalfee, $amount,2);
	    //验证提现金额是否超过总费用
	    if($user->totalfee < 0){
	        return ['code'=>0,'msg'=>'提现金额不足'];
	    }
	    //更新金额
        $user->save();
        //创建审核记录
        $withdrawData = [
          'type' =>1,
            'gid' =>0,
            'openid' =>$user->openid,
            'uid' =>$user->id,
            'realname' =>$user->realname,
            'mobile' =>$user->mobile, 
            'amount' =>$amount,
            'applystatus' =>1,
            'paystatus' =>0,
        ];
        $withdraw = UserWithdraw::create($withdrawData);
        if($withdraw){
            return ['code'=>1,'msg'=>'操作成功','data'=>$user->totalfee];
        }
	    return ['code'=>0,'msg'=>'操作失败'];
	}
	/**
	 * 用户提现列表
	 */
	public function withdrawSearch(){
	    $withdraw = new UserWithdraw();
	    $total = $this->request->param('total');
	    $pageData = $withdraw->getListPageByUid($this->user['id'],$total);
	    return ['code'=>1,'msg'=>'查询成功','data'=>$pageData];
	}
}
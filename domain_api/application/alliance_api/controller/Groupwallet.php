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
// | 社群通讯录控制器
// +----------------------------------------------------------------------

namespace app\alliance_api\controller;

use app\common\controller\AppController;
use app\alliance_api\model\AllianceGroup;
use app\alliance_api\model\AllianceGroupwithdraw;
use app\core_api\model\User;
use think\Validate;

class Groupwallet extends AppController{
	// 初始化
	public function _initialize(){
		parent::_initialize();
		$this->checkLogin();
	}
	
	/**
	 * 圈子钱包
	 */
	public function detail(){
	    $gid = $this->request->param('gid');
	    $group = AllianceGroup::field('id,uid,totalfee')->find($gid);
	    return ['code'=>1,'msg'=>'操作成功','data'=>$group];
	}
	
	/**
	 * 圈子提现
	 */
	public function withdraw(){
	    $gid = $this->request->param('gid');
	    $amount = $this->request->param('amount');
	    //验证金额正确
	    $validate = Validate::make([
	        'amount'  => 'require|float'
	    ]);
	    if (!$validate->check(['amount'=>$amount])) {
	        return ['code'=>0,'msg'=>'金额输入不正确'];
	    }
	     
	    $user = User::field('id,openid,realname,mobile')->find($this->user['id']);
	    $group = AllianceGroup::field('id,uid,totalfee')->find($gid);
	    //验证提现人是否是群主
	    if($this->user['id'] != $group->uid){
	        return ['code'=>0,'msg'=>'权限不足'];
	    }
	    
	    $group->totalfee = bcsub($group->totalfee, $amount,2);
	    //验证提现金额是否超过总费用
	    if($group->totalfee < 0){
	        return ['code'=>0,'msg'=>'提现金额不足'];
	    }
	    //更新金额
	    $group->save();
	    //创建审核记录
	    $withdrawData = [
	        'type' =>1,
	        'gid' =>$gid,
	        'openid' =>$user->openid,
	        'uid' =>$user->id,
	        'realname' =>$user->realname,
	        'mobile' =>$user->mobile,
	        'amount' =>$amount,
	        'applystatus' =>1,
	        'paystatus' =>0,
	    ];
	    $withdraw = AllianceGroupwithdraw::create($withdrawData);
	    if($withdraw){
	        return ['code'=>1,'msg'=>'操作成功','data'=>$group->totalfee];
	    }
	    return ['code'=>0,'msg'=>'操作失败'];
	}
	/**
	 * 用户提现列表
	 */
	public function withdrawSearch(){
	    $gid = $this->request->param('gid');
	    $total = $this->request->param('total');
	    $withdraw = new AllianceGroupwithdraw();
	    $pageData = $withdraw->getListPageByGid($gid,$total);
	    return ['code'=>1,'msg'=>'查询成功','data'=>$pageData];
	}
	
}
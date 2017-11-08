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
// | 群控制器
// +----------------------------------------------------------------------

namespace app\alliance_api\controller;

use app\common\controller\AppController;
use app\alliance_api\model\AllianceGroup;
use app\alliance_api\model\AllianceGroupmember;
use app\alliance_api\service\GroupSvc;
use app\alliance_api\model\AllianceOrder;
use app\core_api\model\User;

class Group extends AppController{
    protected $uncheckLogin = ['search'];
	// 初始化
	public function _initialize(){
		parent::_initialize();
	   if(!in_array($this->request->action, $this->uncheckLogin)){
		    $this->checkLogin();
		}
	}
	
	/**
	 * 我加的群
	 * @return number[]|string[]|unknown[]
	 */
	public function joinList(){
	    $total = $this->request->param('total');
	    $groupmemberMod = new AllianceGroupmember();
	    $pageData = $groupmemberMod->getGroupListByUid($this->user['id'],$total);
	    return ['code'=>1,'msg'=>'操作成功','data'=>$pageData];
	}
	
	/**
	 * 推荐列表
	 * @return number[]|string[]|unknown[]
	 */
	public function topList(){
	    $groupMod = new AllianceGroup();
	    $list = $groupMod->getTopList();
	    return ['code'=>1,'msg'=>'操作成功','data'=>$list];
	}
	
	/**
	 * 查询列表
	 */
	public function search(){
	    $cond = $this->request->param();
	    $total = $this->request->param('total');
	    $groupMod = new AllianceGroup();
	    $pageData = $groupMod->getListPage($cond,$total);
	    return ['code'=>1,'msg'=>'操作成功','data'=>$pageData];
	}
	
	/**
	 *  群信息
	 */
	public function detail(){
	    $gid = $this->request->param('gid');
	    $group = GroupSvc::groupDetail($gid, $this->user['id']);
	    $group['user'] = GroupSvc::groupUser($group, $this->user['id'],true);
	    return ['code'=>1,'msg'=>'查询成功','data'=>$group];
	}
	
	/**
	 *  加入群
	 */
	public function memberJoin(){
	    $gid = $this->request->param('gid');
	    //验证是否加入过群
	    $member = AllianceGroupmember::get(['gid'=>$gid,'uid'=>$this->user['id']]);
	    if($member){
	        return ['code'=>0,'msg'=>'您已加入，请勿重复加入'];
	    }
	    //验证群是否存在
	    $group = AllianceGroup::get($gid);
	    if(empty($group)){
	        return ['code'=>0,'msg'=>'查询失败'];
	    }
	    //验证群是否收费
	    switch($group['memberfeetype']){
	        case 1:
	            //直接加入
	            $member = AllianceGroupmember::create(['gid'=>$gid,'uid'=>$this->user['id']]);
	            if($member){
	                return ['code'=>1,'msg'=>'加入成功','data'=>0];
	            }else{
	                return ['code'=>0,'msg'=>'加入失败'];
	            }
	            break;
	        case 2:
	            //付费加入:生成付款订单
	            $order = AllianceOrder::create([
	               'orderno' => uniqid('G'),
	               'orderdetail' => '加入商友圈',
	               'ordertype'=>'groupjoin',
	               'paytype'=>'wechat',
	               'payfee'=>$group['memberfee'],
	               'ispay'=>0,
	               'gid'=>$group['id'],
	               'gname'=>$group['name'],
	               'uid'=>$this->user['id'],
	               'realname'=>$this->user['realname'],
	            ]);
	            
	            if($order){
	                return ['code'=>1,'msg'=>'订单生成','data'=>$order['id']];
	            }else{
	                return ['code'=>0,'msg'=>'订单生成失败'];
	            }
	            break;
	        default:
	            return ['code'=>0,'msg'=>'查询失败'];
	            
	    }
	}
	/**
	 * 支付成功
	 */
	public function memberJoinPaySuccess(){
	    $orderid = $this->request->param('orderid');
	    $order = AllianceOrder::get($orderid);
	    if($order){//订单存在，成员加入成功
	        $member = AllianceGroupmember::create(['gid'=>$order['gid'],'uid'=>$this->user['id']]);
	        return ['code'=>1,'msg'=>'加入成功'];
	    }else{
	        return ['code'=>0,'msg'=>'加入失败'];
	    }
	    
	}
	
	/**
	 *  退出群
	 */
	public function memberQuit(){
	    $gid = $this->request->param('gid');
	    $uid = $this->user['id'];
	    $touser = AllianceGroupmember::get(['gid'=>$gid,'uid'=>$uid]);
	    $count = $touser->delete(true);
	        if($count == 1){
	            return ['code'=>1,'msg'=>'退出成功'];
	        }else{
	            return ['code'=>0,'msg'=>'已退出或退出失败'];
	        }
	    
	}
	
	/**
	 *  通讯录
	 */
	public function memberSearch(){
	    $gid = $this->request->param('gid');
	    $total = $this->request->param('total');
	    $groupmemberMod = new AllianceGroupmember();
	    $pageData = $groupmemberMod->getMemberListByGid($gid,$total);
	    return ['code'=>1,'msg'=>'操作成功','data'=>$pageData];
	}
	
	/**
	 *  成员详细
	 */
	public function memberDetail(){
	   $gid = $this->request->param('gid');
	   $uid = $this->request->param('uid');
	   $groupmemberMod = new AllianceGroupmember();
	   $user = $groupmemberMod->getMemberDetailByGidAndUid($gid, $uid);
	   return ['code'=>1,'msg'=>'操作成功','data'=>$user];
	   
	}
	
}
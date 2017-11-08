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
use think\image\Image;
use app\core_api\model\UserCard;

class Usersearch extends AppController{
	//初始化
	public function _initialize(){
		parent::_initialize();
	}
	
	public function profileView(){
	    $uid = $this->request->param('uid');
	    $user  = User::field('realname,avatar,gender,birthday,city,province,mobile,issecret,address,addressla,addresslo')->find($uid);
	    if($user){
	        return ['code'=>1,'msg'=>'查询成功','data'=>$user];
	    }else{
	        return ['code'=>0,'msg'=>'用户不存在','data'=>null];
	    }
	}

	public function cardView(){
	    $uid = $this->request->param('uid');
	    $card  = UserCard::field('uid,realname,avatar,corpname,duty,mobile,content,website,qq,email,images,views,likes,comments,address,addressla,addresslo')->find($uid);
	    if($card){
	        return ['code'=>1,'msg'=>'查询成功','data'=>$card];
	    }else{
	        return ['code'=>0,'msg'=>'用户不存在','data'=>null];
	    }
	}
	
}
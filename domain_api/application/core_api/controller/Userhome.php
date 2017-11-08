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
use app\core_api\model\UserCard;
use app\core_api\service\UploadSvc;

class Userhome extends AppController{
	//初始化
	public function _initialize(){
		parent::_initialize();
		$this->checkLogin();
	}
	
	public function getProfile(){
	    $user  = User::field('realname,avatar,gender,birthday,city,province,mobile,issecret,address,addressla,addresslo')->find($this->user['id']);
	    if($user){
	        return ['code'=>1,'msg'=>'查询成功','data'=>$user];
	    }else{
	        return ['code'=>0,'msg'=>'用户不存在','data'=>null];
	    }
	}
	
	public function saveProfile(){
	    $data = $this->request->param();
	    $user = User::field('id,realname,avatar,gender,birthday,city,province,mobile,issecret,address,addressla,addresslo')->find($this->user['id']);
	    $num = $user->save($data);
	    if($num !== false){
	        return ['code'=>1,'msg'=>'保存成功','data'=>$user];
	    }else{
	        return ['code'=>1,'msg'=>'保存失败','data'=>''];
	    }
	    
	}
	
	public function createCard(){
	    $user = User::get($this->user['id']);
	    $data = ['uid'=>$user['id'],'realname'=>$user['realname'],'avatar'=>$user['avatar'],'mobile'=>$user['mobile']];
	    $card = UserCard::get($this->user['id']);
	    if(empty($card)){
	        $num = UserCard::create($data);
	        if($num == 1){
	            $card = $data;
	        }
	    }
	    
	    if($user){
	        return ['code'=>1,'msg'=>'操作成功','data'=>$card];
	    }else{
	        return ['code'=>0,'msg'=>'用户不存在','data'=>null];
	    }
	}
	
	public function getCard(){
	    $card  = UserCard::field('realname,avatar,corpname,duty,mobile,content,website,qq,email,images,views,likes,comments,address,addressla,addresslo')->find($this->user['id']);
	    if($card){
	        return ['code'=>1,'msg'=>'查询成功','data'=>$card];
	    }else{
	        return ['code'=>0,'msg'=>'用户不存在','data'=>null];
	    }
	}
	
	
	public function saveCard(){
	    $data = $this->request->param();
	    if(!empty($data['images'])){
	        $data['images'] = json_encode($data['images']);
	    }
	    $card = UserCard::get($this->user['id']);
	    $num = $card->save($data);
	    if($num !== false){
	        return ['code'=>1,'msg'=>'保存成功','data'=>''];
	    }else{
	        return ['code'=>1,'msg'=>'保存失败','data'=>''];
	    }
	}
	
	
	public function saveCardAvatar(){
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = $this->request->file('imgFile');
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = $this->request->file('imgFile');
	    $rtnData = UploadSvc::uploadFile($file,'usercard');
	    if($rtnData['code']){
	        $card = UserCard::get($this->user['id']);
	        $card->save(['avatar'=>$url]);
	    }
	    return $rtnData;
	}
	
	public function saveProfileAvatar(){
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = $this->request->file('imgFile');
	    $rtnData = UploadSvc::uploadFile($file,'user');
	    if($rtnData['code']){
	        $user = User::get($this->user['id']);
	        $user->save(['avatar'=>$url]);
	    }
	    return $rtnData;
	}
	
    public function uploadImg(){
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = $this->request->file('imgFile');
	    $rtnData = UploadSvc::uploadFile($file,'album');
	    return $rtnData;
	}
	
}
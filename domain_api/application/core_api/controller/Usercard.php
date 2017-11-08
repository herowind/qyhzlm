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
// | 名片信息
// +----------------------------------------------------------------------

namespace app\core_api\controller;

use app\common\controller\AppController;
use app\core_api\model\User;
use app\core_api\model\UserCard;
use app\core_api\service\UploadSvc;

class Usercard extends AppController{
    protected $uncheckLogin = ['viewCard'];
    
	//初始化
	public function _initialize(){
		parent::_initialize();
		if(!in_array($this->request->action(), $this->uncheckLogin)){
		    $this->checkLogin();
		}
	}
	

	/**
	 * 创建名片
	 */
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
	
	/**
	 * 取得自己的名片
	 */
	public function getCard(){
	    $card  = UserCard::field('realname,avatar,corpname,duty,mobile,content,website,qq,email,images,views,likes,comments,address,addressla,addresslo')->find($this->user['id']);
	    if($card){
	        return ['code'=>1,'msg'=>'查询成功','data'=>$card];
	    }else{
	        return ['code'=>0,'msg'=>'用户不存在','data'=>null];
	    }
	}
	
	/**
	 * 保存名片
	 */
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
	        return ['code'=>0,'msg'=>'保存失败','data'=>''];
	    }
	}
	
	/**
	 * 交换名片
	 */
	public function exchangeCard(){
	    $data['uid'] = $this->user['id'];
	    $data['carduid'] = $this->request->param('carduid');
	    
	    $card = UserCard::where($data)->find();
	    if($card){
	        return ['code'=>0,'msg'=>'名片已保存'];
	    }else{
	        //判断对方是否已经加入
	        $exdata['uid'] = $this->request->param('carduid');
	        $exdata['carduid'] = $this->user['id'];
	        $card = UserCard::where($exdata)->find();
	        if($card){
	            //如果对方已申请交换名片，更新申请状态
	            $exdata['applystatus'] = 1;
	            $data['applystatus'] = 1;
	            $card->save($exdata);
	            UserCard::create($data);
	            return ['code'=>0,'msg'=>'交换成功'];
	        }else{
	            UserCard::create($data);
	            return ['code'=>0,'msg'=>'已通知对方'];
	        }
	    }
	    $num = $card->save($data);
	    if($num !== false){
	        return ['code'=>1,'msg'=>'保存成功','data'=>''];
	    }else{
	        
	    }
	}
	
	/**
	 * 删除名片
	 */
	public function removeCard(){
	    $data['uid'] = $this->user['id'];
	    $data['carduid'] = $this->request->param('carduid');
	     
	    $card = UserCard::where($data)->find();
	    if($card){
	        $card->delete(true);
	    }else{
	        return ['code'=>0,'msg'=>'名片不存在'];
	    }
	}	
	
	/**
	 * 上传名片头像
	 */
	public function saveCardAvatar(){
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = $this->request->file('imgFile');
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = $this->request->file('imgFile');
	    $rtnData = UploadSvc::uploadFile($file,'usercard');
	    if($rtnData['code']){
	        $card = UserCard::get($this->user['id']);
	        $card->save(['avatar'=>$rtnData['url']]);
	    }
	    return $rtnData;
	}
	
	/**
	 * 上传名片中的相册
	 */
    public function uploadImg(){
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = $this->request->file('imgFile');
	    $rtnData = UploadSvc::uploadFile($file,'album');
	    return $rtnData;
	}
	
	/**
	 * 浏览名片
	 */
	public function viewCard(){
	    $uid = $this->request->param(uid);
	    $card  = UserCard::field('realname,avatar,corpname,duty,mobile,content,website,qq,email,images,views,likes,comments,address,addressla,addresslo')->find($uid);
	    if($card){
	        return ['code'=>1,'msg'=>'查询成功','data'=>$card];
	    }else{
	        return ['code'=>0,'msg'=>'用户不存在','data'=>null];
	    }
	}
	
}
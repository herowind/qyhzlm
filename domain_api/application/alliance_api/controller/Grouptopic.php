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
// | 群话题控制器
// +----------------------------------------------------------------------

namespace app\alliance_api\controller;

use app\common\controller\AppController;
use app\core_api\service\UploadSvc;
use app\alliance_api\model\AllianceTopic;
use app\alliance_api\service\GroupSvc;
use app\alliance_api\model\AllianceOrder;
use app\alliance_api\model\AllianceGroup;
use app\alliance_api\model\AllianceGroupmember;
use app\alliance_api\model\AllianceTopiccomment;
use app\core_api\model\User;

class Grouptopic extends AppController{
	//初始化
	public function _initialize(){
		parent::_initialize();

	}
	/**
	 * 查询列表
	 */
	public function search(){
	    $this->checkLogin();
	    $search = $this->request->only('gid,total,page,keywords');
	    $search['loginUid'] = $this->user['id'];
	    $pageData = GroupSvc::groupTopicList($search);
	    return ['code'=>1,'msg'=>'查询成功','data'=>$pageData];
	}
	
	/**
	 * 详细
	 */
	public function detail(){
	    $this->checkLogin();
	    $detail = GroupSvc::groupTopicDetail($detail->id);
	    return ['code'=>1,'msg'=>'操作成功','data'=>$detail];
	}
	
	/**
	 * 创建
	 */
	public function save(){
	    $this->checkLogin();
	    $data = $this->request->param();
	    $group = AllianceGroup::get($data['gid']);
	    //验证群是否存在
	    if(empty($group)){
	        return ['code'=>0,'msg'=>'查询失败'];
	    }
	    
	    //验证是否群成员
	    $member = AllianceGroupmember::get(['gid'=>$data['gid'],'uid'=>$this->user['id']]);
	    if(empty($member)){
	        return ['code'=>0,'msg'=>'您还未加入'];
	    }
	    
	    //验证禁言时间
	    if($member['bantime']>time()){
	        $bantimestr  =  date('Y-m-d H:i',$member['bantime']);
	        return ['code'=>0,'msg'=>"您被禁言了，禁言截止：{$bantimestr}"];
	    }
	    
	    $topicData = [
	        'gid'          => $group['id'],
	        'gname'        => $group['name'],
	        'uid'          => $this->user['id'],
	        'realname'     => $this->user['realname'],
	        'avatar'       => $this->user['avatar'],
	        'mediatype'    => $data['mediatype'],
	        'content'      => $data['content'],
	        'type'         => $data['type'],
	    ];
	    
	    switch($data['mediatype']){
	        case 'image':
	            if(!empty($data['media'])){
	                $media = [];
	                foreach ($data['media'] as $key=>$val){
	                    $media[] = ['o'=>$val['o'],'t'=>$val['t']];
	                }
	                $topicData['media'] = json_encode($media);
	            }
	            break;
	        case 'object':
	            if(!empty($data['media'])){
	                $topicData['media'] = json_encode($data['media'],JSON_UNESCAPED_UNICODE);
	            }
	            break;
	    }
	    $detail = AllianceTopic::create($topicData);
	    $detail = GroupSvc::groupTopicDetail($detail->id);
		if($detail!==false){
		    return ['code'=>1,'msg'=>'操作成功','data'=>$detail];
		}else{
		    return ['code'=>0,'msg'=>'操作失败'];
		}
	}
	
	/**
	 * 删除话题
	 */
	public function remove(){
	    $this->checkLogin();
	    $topicid = $this->request->param('topicid');
	    $topic = AllianceTopic::get($topicid);
	    if($topic && $topic->uid == $this->user['id']){
	        $topic->delete();
	        return ['code'=>1,'msg'=>'删除成功'];
	    }else{
	        return ['code'=>1,'msg'=>'删除失败'];
	    }
	}
	
	/**
	 * 取得话题作者信息
	 */
	public function topicUser(){
	    $this->checkLogin();
	    $user = User::field('id,realname,avatar')->find($this->request->param('uid'));
	    return ['code'=>1,'msg'=>'操作成功','data'=>$user];
	}
	
	/**
	 * 上传图片
	 */
	public function uploadImage(){
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = $this->request->file('imgFile');
	    $rtnData = UploadSvc::uploadFile($file,'album');
	    return $rtnData;
	}
	
	/**
	 *  打赏
	 */
	public function memberReward(){
	    $this->checkLogin();
	    $data = $this->request->only('gid,topicid,payfee');
	    
	    $group = AllianceGroup::get($data['gid']);
	    if(empty($group)){
	        return ['code'=>0,'msg'=>'查询失败'];
	    }
	    
	    $topic = AllianceTopic::get($data['topicid']);
	    if(empty($topic)){
	        return ['code'=>0,'msg'=>'查询失败'];
	    }
	
	    //付费加入:生成付款订单
	    $order = AllianceOrder::create([
	        'orderno' => uniqid('T'),
	        'orderdetail' => '打赏',
	        'ordertype'=>'reward',
	        'paytype'=>'wechat',
	        'payfee'=>$data['payfee'],
	        'ispay'=>0,
	        'gid'=>$group['id'],
	        'gname'=>$group['name'],
	        'uid'=>$this->user['id'],
	        'realname'=>$this->user['realname']||'匿名',
	        'touid'=>$topic['uid'],
	        'torealname'=>$topic['realname'],
	        'topicid'=>$topic['id'],
	    ]);
	     
	    if($order){
	        return ['code'=>1,'msg'=>'订单生成',data=>$order['id']];
	    }else{
	        return ['code'=>0,'msg'=>'订单生成失败'];
	    }
	}
	
	/**
	 *  打赏成功
	 */
	public function memberRewardPaySuccess(){
	    $this->checkLogin();
	    $orderid = $this->request->param('orderid');
	    $order = AllianceOrder::get($orderid);
	    if($order){//订单存在，成员加入成功
	        $topic = AllianceTopic::field('id,rewards,rewardarray')->find($order['topicid']);
	        $rewardarray = $topic->rewardarray;
	        $rewardarray[] = [$this->user['id'],$this->user['realname'],$order['payfee']];
	        $topic->rewards = count($rewardarray);
	        $topic->rewardarray = json_encode($rewardarray,JSON_UNESCAPED_UNICODE);
	        $topic->save();
	        return ['code'=>1,'msg'=>'打赏成功','data'=>$topic];
	    }else{
	        return ['code'=>0,'msg'=>'打赏失败'];
	    }

        
	}

	/**
	 *  点赞
	 */
	public function memberLike(){
	    $this->checkLogin();
	    $params = $this->request->only('gid,topicid');
	    $islike = true;
        $topic = AllianceTopic::field('id,likes,likearray')->find($params['topicid']);
        $likearray = $topic->likearray;
        foreach ($likearray as $key => $val){
            if($val[0]==$this->user[id]){
                //取消点赞
                array_splice($likearray,$key,1);
                $topic->likes = count($likearray);
                $topic->likearray = json_encode($likearray,JSON_UNESCAPED_UNICODE);
                $islike = false;
                break;
            }
        }
        if($islike){
            if(empty($likearray)){
                $likearray = [];
            }
            $likearray[] = [$this->user['id'],$this->user['realname']];
            $topic->likes = count($likearray);
            $topic->likearray = json_encode($likearray,JSON_UNESCAPED_UNICODE);
        }
        $topic->save();
        return ['code'=>1,'msg'=>'操作成功','data'=>$topic];
	}
	
	/**
	 *  comment
	 */
	public function memberCommentSave(){
	    $this->checkLogin();
	    //验证是否群成员
	    $member = AllianceGroupmember::where(['gid'=>$this->request->param('gid'),'uid'=>$this->user['id']])->find();
	    if(empty($member)){
	        return ['code'=>0,'msg'=>'您还未加入'];
	    }
	     
	    //验证禁言时间
	    if($member['bantime']>time()){
	        $bantimestr  =  date('Y-m-d H:i',$member['bantime']);
	        return ['code'=>0,'msg'=>"您被禁言了，禁言截止：{$bantimestr}"];
	    }
	    
	    $commentData = $this->request->only('topicid,content,touid,torealname,type');
	    $commentData['uid'] = $this->user['id'];
	    $commentData['realname'] = $this->user['realname'];
	    $comment = AllianceTopiccomment::create($commentData);
	    //增加数量
	    AllianceTopic::update(['comments'=>['exp','comments+1']],['id'=>$commentData['topicid']]);
	    if($comment){
	        return ['code'=>1,'msg'=>'评论成功','data'=>$comment];
	    }else{
	        return ['code'=>0,'msg'=>'评论失败'];
	    }
	    
	}
	
	/**
	 *  删除comment
	 */
	public function memberCommentRemove(){
	    $this->checkLogin();	     
        
	    $comment = AllianceTopiccomment::get($this->request->param('commentid'));
	    if($comment && $comment->uid == $this->user['id']){
	        $comment->delete(true);
	        return ['code'=>1,'msg'=>'删除成功'];
	    }else{
	        return ['code'=>0,'msg'=>'删除失败'];
	    }	     
	}	
}
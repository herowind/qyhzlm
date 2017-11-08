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
// | 社群模型
// +----------------------------------------------------------------------

namespace app\alliance_api\model;

use app\common\model\CommonMod;
class AllianceTopic extends CommonMod{
    //获得评论信息(非获取器)
    public function getCommentarrayAttr($value,$data){
        if($data['comments']>0){
            $commentarray = AllianceTopiccomment::where(['topicid'=>$data['id']])->order('id')->limit(10)->select();
        }
        return empty($commentarray)?[]:$commentarray;
    }
    
    public function getActionhiddenAttr($value){
        return true;
    }
    
    public function getCreateTimeAttr($value){
       $data = $this->formatTime($value);
       return $data;
    }
    
    public function getMediaAttr($value,$data){
        return json_decode($data['media'],true);
    }
    
    public function getLikearrayAttr($value,$data){
        if($data['likearray']){
            return json_decode($data['likearray'],true);
        }
        return [];
    }
    
    public function getRewardarrayAttr($value,$data){
        if($data['rewardarray']){
            return json_decode($data['rewardarray'],true);
        }
        return [];
        
    }
}
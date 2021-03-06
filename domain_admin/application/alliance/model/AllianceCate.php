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
// | 分类模型
// +----------------------------------------------------------------------

namespace app\alliance\model;

use app\common\model\CommonMod;

class AllianceCate extends CommonMod{
    /**
     * 根据条件分页查询
     * @param array $search
     */
    public function listPageBySearch($search=[]){
        $where = [];
        //显示表单列表
        $where['pid'] = $search['pid']?$search['pic']:0;

        $listPage = $this->where($where)->order('sort desc')->paginate(20);
        $pageData['list'] = $listPage->all();
        $pageData['page'] = $listPage->render();
        return $pageData;
    }
    
    /**
     * 标签
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getTagListAttr($value,$data){
        $tagList = explode(' ', $data['tags']) ;
        return $tagList;
    }
    
    public function getBindAttributeAttr($value){
        return json_decode($value,true);
    }
    
    public function getBindFormAttr($value){
        return json_decode($value,true);
    }

    public function setBindAttributeAttr($value,$data){
        $bindAttribute=json_encode(['id'=>$data['attribute_id'],'title'=>$data['attribute_title']],JSON_UNESCAPED_UNICODE);
        return $bindAttribute;
    }
    
    public function setBindFormAttr($value,$data){
        $bindForm=json_encode(['id'=>$data['form_id'],'title'=>$data['form_title']],JSON_UNESCAPED_UNICODE);
        return $bindForm;
    }
}
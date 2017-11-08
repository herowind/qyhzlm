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
// | 信息模型
// +----------------------------------------------------------------------

namespace app\alliance_api\model;

use app\common\model\CommonMod;
use app\alliance_api\service\AppCacheSvc;

class AllianceInfo extends CommonMod{
    public function getListPage($search){
        $where['check_status'] = ['egt','1'];
        if($search['cate_id']){
            $where[] = ['exp',"FIND_IN_SET({$search['cate_id']},cate_ids)"];
        }
        	
        if(isset($search['total']) && is_int($search['total']) && $search['total']>0){
            //不查询总数量
        }else{
            //查询总数量
            $search['total'] = false;
        }
        $listPage = $this->field(true)
                         ->where($where)
                         ->paginate(10,$search['total']);
        $pageData['list'] = $listPage->all();
        foreach ($pageData['list'] as  $key => $val){
            $pageData['list'][$key]['cate'] = $this->getCateAttr('',$val);
        }
        $pageData['page']['hasMore']  = $listPage->currentPage() < $listPage->lastPage();
        $pageData['page']['nextPage'] = $listPage->currentPage()+1;
        $pageData['page']['total'] = $listPage->total();                    
        return $pageData;
    }
    
    public function getCateAttr($value,$data){
        $cateTree = AppCacheSvc::getCateTree();
        $cate_ids = explode(',', $data['cate_ids']);
        $cate = null;
        foreach ($cateTree as $val){
            if($val['id'] == $cate_ids[0]){
                $cate['id'] = $val['id'];
                $cate['title'] = $val['title'];
                foreach ($val['_child'] as $subVal){
                    $cate['_child']['id'] = $subVal['id'];
                    $cate['_child']['title'] = $subVal['title'];
                    break;
                }
                break;
            }
        }
        return $cate;
    }
    
    public function getPicsAttr($value,$data){
        return json_decode($data['pics'],true);
    }
    
    /**
     * 标签
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getTagsAttr($value,$data){
        $tagList = explode(' ', $data['tags']) ;
        return $tagList;
    }
}
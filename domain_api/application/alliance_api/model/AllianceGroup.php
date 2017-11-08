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
use app\alliance_api\service\AppCacheSvc;
use think\Db;

class AllianceGroup extends CommonMod{
    /**
     * 搜索社群
     */    
    public function getListPage($cond=[],$total=false){
        $where['isveryfy'] = 1;
        $where['isallowsearch'] = 1;
//         if($search['cate_id']){
//             $where[] = ['exp',"FIND_IN_SET({$search['cate_id']},cate_ids)"];
//         }
        if(isset($cond['type']) && !empty($cond['type'])){
            $where['type'] = $cond['type'];
        }
        
        if(isset($cond['keywords']) && !empty($cond['keywords'])){
            $where[] = ['name|fullname','like',"%{$cond['keywords']}%"];
        }
        
        $listPage = Db::view('AllianceGroup', 'id,name,fullname,content,logo,memberfeetype,type,isallowsearch,isveryfy,members,topics,views,istop,tagids')
        ->view('User', ['id as uid','avatar as user_avatar','realname as user_realname'], "AllianceGroup.uid = User.id")
        ->where($where)
        ->order('istop desc')
        ->paginate(10);
        

        $pageData['list'] = $listPage->all();
        $pageData['page']['hasMore']  = $listPage->currentPage() < $listPage->lastPage();
        $pageData['page']['nextPage'] = $listPage->currentPage()+1;
        $pageData['page']['total'] = $listPage->total();                    
        return $pageData;
    }
    
    /**
     * 获得推荐列表
     */
    public function getTopList(){
        $where['istop'] = 1;
        $where['isveryfy'] = 1;
        $where['isallowsearch'] = 1;
        $list = $this->where($where)->select();
        return $list;
    }
}
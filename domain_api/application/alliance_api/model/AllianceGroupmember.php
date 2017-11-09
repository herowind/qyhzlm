<?php
// +----------------------------------------------------------------------
// | 联盟管理平台
// +----------------------------------------------------------------------
// | Copyright (c) 2017~2020 http://www.qyhzlm.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( 商业版权，禁止传播，违者必究 )
// +----------------------------------------------------------------------
// | Author: oliver <2244115959@qq.com>
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 用户社群模型
// +----------------------------------------------------------------------
namespace app\alliance_api\model;

use app\common\model\CommonMod;
use think\Db;

class AllianceGroupmember extends CommonMod
{
    public function getIsbanAttr($val){
        if($val>$_SERVER[REQUEST_TIME]){
           return 1;
        }else{
            return 0;
        }
    }

    /**
     * 获得我的社群
     * 
     * @param unknown $uid            
     */
    public function getGroupListByUid($uid, $total = false)
    {
        $listPage = Db::view('AllianceGroupmember', 'uid,gid,isadmin,iscreator,isinvite,unreads')->view('AllianceGroup', 'id,name,fullname,logo,content', "AllianceGroupmember.gid = AllianceGroup.id and AllianceGroupmember.uid ={$uid}")->paginate(10, $total);
        
        $pageData['list'] = $listPage->all();
        $pageData['page']['hasMore'] = $listPage->currentPage() < $listPage->lastPage();
        $pageData['page']['nextPage'] = $listPage->currentPage() + 1;
        $pageData['page']['total'] = $listPage->total();
        return $pageData;
    }
    /**
     * 查询通讯录
     * @param unknown $gid
     * @param string $total
     */
    public function getMemberListByGid($gid, $total = false)
    {
        $listPage = Db::view('AllianceGroupmember', 'uid,gid,isadmin,iscreator')->view('User', 'id,realname,avatar,city', "AllianceGroupmember.uid = User.id and AllianceGroupmember.gid={$gid}")->paginate(10, $total);
        $pageData['list'] = $listPage->all();
        foreach ($pageData['list'] as $key => $val) {
            
            if ($val['isadmin']) {
                if ($val['iscreator']) {
                    $pageData['list'][$key]['rolename'] = '创始人';
                } else {
                    $pageData['list'][$key]['rolename'] = '管理员';
                }
            }else{
                $pageData['list'][$key]['rolename'] = '';
            }
        }
        
        $pageData['page']['hasMore'] = $listPage->lastPage()!=0 && $listPage->currentPage()<$listPage->lastPage();
        $pageData['page']['currentPage'] = $listPage->currentPage();
        $pageData['page']['hasNextPage'] = $listPage->currentPage()<$listPage->lastPage();
        $pageData['page']['hasPrePage'] = $listPage->currentPage()>1;
        $pageData['page']['lastPage'] = $listPage->lastPage();
        $pageData['page']['total'] = $listPage->total();
        return $pageData;
    }
    
    public function getMemberDetailByGidAndUid($gid, $uid)
    {
        $detail = Db::view('AllianceGroupmember', 'uid,gid,isadmin,iscreator,bantime,create_time')
        ->view('User', 'id,realname,avatar,city,birthday,gender', "AllianceGroupmember.uid = User.id")
        ->where('gid',$gid)
        ->where('uid',$uid)
        ->find();
        $detail['isban'] = $this->getIsbanAttr($detail->bantime);
        return $detail;
    }    
    /**
     * 通讯录，踢人，禁言，设置管理员
     * @param unknown $gid
     * @param array $cond
     * @param string $total
     */
    public function search($gid,$cond=[],$total=false)
    {
        $where[] = ['gid','=',$gid];
        $order = 'isadmin desc and create_time desc';
        //管理者·非管理者查询
        if(isset($cond['isadmin'])){
            $where[] = ['isadmin','=',isadmin];
        }
        
        if(isset($cond['keywords']) && !empty($cond['keywords'])){
            $where[] = ['realname','like',"%{$cond['keywords']}%"];
        }
        
        if(isset($cond['isban']) && $cond['isban']){
            $order = 'isadmin desc and bantime desc and create_time desc';
        }
        
        $listPage = Db::view('AllianceGroupmember', 'uid,gid,isadmin,iscreator,create_time,bantime')
        ->view('User', 'id,realname,avatar,city', "AllianceGroupmember.uid = User.id")
        ->where($where)
        ->order($order)
        ->paginate(10, $total);

        
        $pageData['list'] = $listPage->all();
        $currentTime = time();
        foreach ($pageData['list'] as $key => $val) {
    
            if ($val['isadmin']) {
                if ($val['iscreator']) {
                    $pageData['list'][$key]['rolename'] = '创始人';
                } else {
                    $pageData['list'][$key]['rolename'] = '管理员';
                }
            }else{
                $pageData['list'][$key]['rolename'] = '';
            }
            
            if($val['bantime']>$currentTime){
                $pageData['list'][$key]['isban'] = 1;
            }else{
                $pageData['list'][$key]['isban'] = 0;
            }
        }
    
        $pageData['page']['hasMore'] = $listPage->lastPage()!=0 && $listPage->currentPage()<$listPage->lastPage();
        $pageData['page']['currentPage'] = $listPage->currentPage();
        $pageData['page']['hasNextPage'] = $listPage->currentPage()<$listPage->lastPage();
        $pageData['page']['hasPrePage'] = $listPage->currentPage()>1;
        $pageData['page']['lastPage'] = $listPage->lastPage();
        $pageData['page']['total'] = $listPage->total();
        return $pageData;
    }
}
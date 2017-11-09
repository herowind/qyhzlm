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
// | 应用缓存
// +----------------------------------------------------------------------
namespace app\alliance_api\service;

use think\facade\Cache;
use app\alliance_api\model\AllianceGroup;
use app\alliance_api\model\AllianceGroupmember;
use app\alliance_api\model\AllianceTopic;
use app\alliance_api\model\AllianceTopiccomment;

class GroupSvc
{
    
    public static function groupDetail($gid,$loginUid){
        $group = AllianceGroup::get($gid);
        if($group['isveryfy']){
            //审核通过的群
            return $group;
        }else{
            //审核中的群
            if($group['uid'] == $loginUid){
                //创始人可查看
                return $group;
            }else{
                //非管理员报错
                return null;
            }
        }
    }
    /**
     * 查询登录用户
     * @param unknown $group
     * @param unknown $loginUid
     * @param string $forceRefresh
     * @return NULL|number|\app\alliance_api\model\AllianceGroupmember
     */
    public static function groupUser($group, $loginUid, $forceRefresh = false)
    {
        if ($forceRefresh) {
            $groupUser = null;
        } else {
            $groupUser = Cache::get("CA_GROUPUSER_{$group['id']}_{$loginUid}");
        }
        if (empty($groupUser)) {
            $groupUser = AllianceGroupmember::get([
                'gid' => $group['id'],
                'uid' => $loginUid
            ]);
            if (empty($groupUser)) {// 未加入群
                $groupUser['isjoin'] = 0;
                // 评论权限
                $groupUser['iscomment'] = 0;
                
                // 话题浏览权限
                switch ($group['topicvisibility']) {
                    case 1:
                        $groupUser['istopicvisibility'] = 1;
                        break;
                    case 2:
                        $groupUser['istopicvisibility'] = 0;
                        break;
                    case 3:
                        $groupUser['istopicvisibility'] = 0;
                        break;
                    default:
                        $groupUser['istopicvisibility'] = 0;
                        break;
                }
                // 通讯录浏览权限
                switch ($group['membervisibility']) {
                    case 1:
                        $groupUser['ismembervisibility'] = 1;
                        break;
                    case 2:
                        $groupUser['ismembervisibility'] = 0;
                        break;
                    case 3:
                        $groupUser['ismembervisibility'] = 0;
                        break;
                    default:
                        $groupUser['ismembervisibility'] = 0;
                        break;
                }
            }else{
                $groupUser['isjoin'] =1;
                // 评论权限
                $groupUser['iscomment'] = 1;
                
                // 话题浏览权限
                switch ($group['topicvisibility']) {
                    case 1:
                        $groupUser['istopicvisibility'] = 1;
                        break;
                    case 2:
                        $groupUser['istopicvisibility'] = 1;
                        break;
                    case 3:
                        if($groupUser['isadmin']){
                            $groupUser['istopicvisibility'] = 1;
                        }else{
                            $groupUser['istopicvisibility'] = 0;
                        }
                        
                        break;
                    default:
                        $groupUser['istopicvisibility'] = 0;
                        break;
                }
                // 通讯录浏览权限
                switch ($group['membervisibility']) {
                    case 1:
                        $groupUser['ismembervisibility'] = 1;
                        break;
                    case 2:
                        $groupUser['ismembervisibility'] = 0;
                        break;
                    case 3:
                        if($groupUser['isadmin']){
                            $groupUser['ismembervisibility'] = 1;
                        }else{
                            $groupUser['ismembervisibility'] = 0;
                        }
                        break;
                    default:
                        $groupUser['ismembervisibility'] = 0;
                        break;
                }                
            }
            Cache::set("CA_GROUPUSER_{$group['id']}_{$loginUid}",$groupUser);
        }
        return $groupUser;
    }
    /**
     * 获得置顶的帖子
     * @param unknown $gid
     */
    public function groupTopTopicList($gid){
        $where['gid'] = $gid;
        $where['toptime'] = ['egt',time()];
        $topList = AllianceTopic::where($where)->limit(10)->select();
        return $topList;
    }

    /**
     * 搜索话题列表
     * @param unknown $search
     */
    public static function groupTopicList($search){
        // 获得社群
        $group = self::groupDetail($search['gid'], $search['loginUid']);
        // 登录用户信息
        $groupUser = self::groupUser($group, $search['loginUid']);
        $topicMod = new AllianceTopic();
        if($groupUser['istopicvisibility']){
            $where[] = ['gid','=',$search['gid']];
            if(isset($search['total']) && is_int($search['total']) && $search['total']>0){
                //不查询总数量
            }else{
                //查询总数量
                $search['total'] = false;
            }
            $listPage = $topicMod->field(true)
            ->where($where)
            ->order('istop desc , id desc')
            ->paginate(15,$search['total']);
            $pageData['list'] = $listPage->all();
            $pageData['page']['hasMore']  = $listPage->currentPage() < $listPage->lastPage();
            $pageData['page']['nextPage'] = $listPage->currentPage()+1;
            $pageData['page']['total'] = $listPage->total();
        }else{
            $where[] = ['gid','=',$search['gid']];
            $pageData['list'] = $topicMod->where($where)->order('istop desc , id desc')->limit(10)->select();
            $pageData['page']['hasMore']  = false;
            $pageData['page']['nextPage'] = 1;
            $pageData['page']['total'] = 10;
        }
        foreach ($pageData['list'] as $key => $val){
            $pageData['list'][$key]->append(['commentarray']);
        }
        return $pageData;
    }
    /**
     * 搜索话题详情
     * @param unknown $search
     */
    public static function groupTopicDetail($topicid){
        $detail = AllianceTopic::get($topicid);
        $detail->append(['commentarray']);
        return $detail;
    }
}

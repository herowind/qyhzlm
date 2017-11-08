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
// | 应用缓存
// +----------------------------------------------------------------------
namespace app\alliance_api\service;

use com\utils\FncTree;
use app\alliance_api\model\AllianceCate;
use think\facade\Cache;


class AppCacheSvc{
    public static function getCateTree(){
        //分类数据
        $cateTree = Cache::get('CA_CATE_TREE');
        if(empty($cateTree)){
            $list = AllianceCate::all();
            $cateTree = FncTree::listToTreeNoKey($list->toArray());
            Cache::set('CA_CATE_TREE',$cateTree);
        }
        return $cateTree;
    }
}

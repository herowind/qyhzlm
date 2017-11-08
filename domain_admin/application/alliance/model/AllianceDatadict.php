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
// | 数据字典模型
// +----------------------------------------------------------------------

namespace app\alliance\model;

use app\common\model\CommonMod;

class AllianceDatadict extends CommonMod{
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

}
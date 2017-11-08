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
// | 车主模型
// +----------------------------------------------------------------------

namespace app\pintu\model;

use app\common\model\CommonMod;

class PintuDriver extends CommonMod{
    //protected $table = 'app_pintu_driver';
    /**
     * 根据条件分页查询
     * @param array $search
     */
    public function listPageBySearch($search=[]){
        $listPage = $this->order('id desc')->paginate(10);
        $pageData['list'] = $listPage->all();
        $pageData['page'] = $listPage->render();
        return $pageData;
    }
    
    /**
     * 审核状态
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getStatusTextAttr($value,$data){
        $text = [-1=>'删除',0=>'禁用',1=>'正常',2=>'待审核'];
        return $text[$data['status']];
    }
    
    /**
     * 手机验证状态
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getMobileValidatedTextAttr($value,$data){
        $text = [0=>'未验证',1=>'已验证'];
        return $text[$data['mobile_validated']];
    }
    
    
    
}
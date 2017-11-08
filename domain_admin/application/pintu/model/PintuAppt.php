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
// | 预约单模型
// +----------------------------------------------------------------------

namespace app\pintu\model;

use app\common\model\CommonMod;

class PintuAppt extends CommonMod{
    //protected $table = 'app_pintu_driver';
    /**
     * 根据条件分页查询
     * @param array $search
     */
    public function listPageBySearch($search=[]){
        $where = [];
        //预约类型
        if(!empty($search['type'])){
            $where['type'] = $search['type'];
        }
        
        //出发地城市
        if(!empty($search['from_city_name'])){
            $where['from_city_name'] = $search['from_city_name'];
        }
        
        //目的地城市
        if(!empty($search['to_city_name'])){
            $where['to_city_name'] = $search['to_city_name'];
        }
        
        //关键词
        if(!empty($search['keywords'])){
            $where['mobile|remark'] = ['like',"%{$search['keywords']}%"];
        }

        $listPage = $this->where($where)->order('id desc')->paginate(10);
        $pageData['list'] = $listPage->all();
        $pageData['page'] = $listPage->render();
        return $pageData;
    }
    
    /**
     * 预约单类型
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getTypeTextAttr($value,$data){
        $text = [1=>'按天包车',2=>'城际顺风车'];
        return $text[$data['type']];
    }
    
    /**
     * 显示状态
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getIsDisplayTextAttr($value,$data){
        $text = [0=>'内部单',1=>'外部单'];
        return $text[$data['is_display']];
    }
    
    /**
     * 分配状态
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getIsAllotTextAttr($value,$data){
        $text = [0=>'未分配',1=>'已分配'];
        return $text[$data['is_allot']];
    }
    
    
    public function getCustDatetimeAttr($value,$data){
        return $data['cust_date'].' '.$data['cust_time'];
    }
    
    public function setCustDatetimeAttr($value,$data){
        $datetime = explode(' ',$data['cust_datetime']);
        $this->setAttr('cust_date', $datetime[0]);
        $this->setAttr('cust_time', $datetime[1]);
        return $value;
    }

}
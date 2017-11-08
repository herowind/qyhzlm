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
// | 站点模型
// +----------------------------------------------------------------------

namespace app\pintu\model;

use app\common\model\CommonMod;
use com\utils\ClsPinYin;

class PintuStation extends CommonMod{
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
     * 根据父ID分页查询
     * @param number $pid
     * @return unknown
     */
    public function listPageByPid($pid=0){
        $where['pid'] 		= $pid;
        $listPage = $this->where($where)->order('is_hot desc ,sort desc')->paginate(10);
        $pageData['list'] = $listPage->all();
        $pageData['page'] = $listPage->render();
        return $pageData;
    }
    
    public function setNamePinyinAttr($value){
        $clsPinYin = new ClsPinYin();
        return $clsPinYin->getAllPY($value);
    }
    
    public function setNameQuickAttr($value){
        $clsPinYin = new ClsPinYin();
        return $clsPinYin->getFirstPY($value);
    }
    
    /**
     * 区域名称：用于前台JS筛选
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getNameTextAttr($value,$data){
        $text = $data['name'].'|'.$data['name_pinyin'].'|'.$data['name_quick'];
        return $text;
    }
    
    /**
     * 热门状态
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getIsHotTextAttr($value,$data){
        $text = [0=>'正常',1=>'热门'];
        return $text[$data['is_hot']];
    }
    /**
     * 显示状态
     * @param unknown $value
     * @param unknown $data
     * @return string
     */
    public function getIsDisplayTextAttr($value,$data){
        $text = [0=>'隐藏',1=>'显示'];
        return $text[$data['is_display']];
    }
    
    
    
}
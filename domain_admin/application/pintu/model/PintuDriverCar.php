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
// | 车主车辆模型
// +----------------------------------------------------------------------

namespace app\pintu\model;

use app\common\model\CommonMod;
use think\Db;

class PintuDriverCar extends CommonMod{
    //protected $table = 'app_pintu_driver_car';
    /**
     * 根据条件分页查询
     * @param array $search
     */
    public function listPageBySearch($search=[]){
        $listPage = Db::view('PintuDriverCar','id,driver_id,type,num,seat,color,buy_year,remark')
                      ->view('PintuDriver','realname,mobile,call_num','PintuDriverCar.driver_id = PintuDriver.id')
                      ->paginate(10);
        
        $pageData['list'] = $listPage->all();
        $pageData['page'] = $listPage->render();
        return $pageData;
    }
    
    
}
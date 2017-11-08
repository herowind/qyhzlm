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
// | 车主车辆验证器
// +----------------------------------------------------------------------

namespace app\pintu\service\validate;

use think\Validate;

class PintuDriverCarValid extends Validate{
    
    protected $rule = [
        'driver_id' =>  ['require'],
        'type'      =>  ['require'],
        'num'       =>  ['require'],
        'seat'      =>  ['number'],
    ];
    
    protected $message = [
        'driver_id.require' => '车主不存在',
        'type.require'      => '车型号不能为空',
        'num.require'       => '车牌号不能为空',
        'seat.number'       => '座位数必须是数字',
    ];
    
    protected $scene = [
        'add'   =>  ['driver_id','type','num','seat'],
    ];
}
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
// | 预约单验证器
// +----------------------------------------------------------------------

namespace app\pintu\service\validate;

use think\Validate;

class PintuApptValid extends Validate{
    
    protected $rule = [
        'from_city_name'=>  ['require'],
        'to_city_name'  =>  ['require'],
        'cust_mobile'   =>  ['require','mobile'],
        'cust_num'      =>  ['require','integer'],
        'cust_time'     =>  ['require','date'],
        'cust_days'     =>  ['require','number'],
    ];
    
    protected $message = [
        'from_city_name'        => '始发城市不能为空',
        'to_city_name'          => '目的城市不能为空',
        'cust_mobile.require'   => '客户手机号不能为空',
        'cust_mobile.mobile'    => '客户手机号格式错误',
        'cust_num.require'      => '乘客人数不能为空',
        'cust_num.integer'      => '请输入正确的乘客数',
        'cust_days.require'      => '包车天数不能为空',
        'cust_days.number'       => '请选择正确的包车天数',
    ];
    
    protected $scene = [
        'type_1'   =>  ['from_city_name','cust_mobile','cust_num','cust_days'],
        'type_2'  =>  ['from_city_name','to_city_name','cust_mobile','cust_num'],
    ];
}
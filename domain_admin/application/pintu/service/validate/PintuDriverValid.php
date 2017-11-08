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
// | 车主验证器
// +----------------------------------------------------------------------

namespace app\pintu\service\validate;

use think\Validate;

class PintuDriverValid extends Validate{
    
    protected $rule = [
        'mobile'    =>  ['require','mobile','unique'=>'PintuDriver'],
        'password'  =>  ['require','min'=>6],
        'realname'  =>  ['require','chsAlphaNum','min'=>2],
    ];
    
    protected $message = [
        'mobile.require'    => '手机号不能为空',
        'mobile.mobile'     => '手机号格式错误',
        'mobile.unique'     => '手机号已存在',
        'password.min'      => '密码至少6位',
        'realname.chsAlphaNum' => '姓名必须是汉字或字母',
    ];
    
    protected $scene = [
        'add'   =>  ['mobile','password'],
    ];
}
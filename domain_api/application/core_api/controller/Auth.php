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
// | 登录
// +----------------------------------------------------------------------
namespace app\core_api\controller;

use app\common\controller\AppController;
use app\core_api\service\LoginService;

/**
 * 描述：个人管理API
 * @author oliver
 *
 */
class Auth extends AppController{ 
	
	//初始化
	protected function _initialize(){
		parent::_initialize();
	}
	
	public function login() {
		$rtnData = LoginService::loginwx($this->request->header());
		return $rtnData;
	}
	


}
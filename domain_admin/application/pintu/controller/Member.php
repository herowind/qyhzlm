<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------
namespace app\screen\controller;

use app\common\controller\AuthController;

/**
 * 客户管理
 * @author oliver
 *
 */
class Member extends AuthController{
	
	public function _initialize(){
		//parent::_initialize();
	}
	
	/**
	 * 用户列表
	 */
	public function memberList() {
	    return $this->fetch();
	}
	
	/**
	 * 黑名单列表
	 */
	public function blackList() {
	    return $this->fetch();
	}	

	/**
	 * 管理员列表
	 */
	public function adminList() {
		return $this->fetch();
	}
	
}
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
 * 设置
 * @author oliver
 *
 */
class Setting extends AuthController{
	
	public function _initialize(){
		//parent::_initialize();
	}

	/**
	 * 设置
	 */
	public function index() {
		return $this->fetch();
	}
	
	/**
	 * 通知设置
	 */
	public function notice() {
	    return $this->fetch();
	}
	
	
}
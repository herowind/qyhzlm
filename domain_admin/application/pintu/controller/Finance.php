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
 * 微屏幕游戏管理
 * @author oliver
 *
 */
class Message extends AuthController{
	
	public function _initialize(){
		//parent::_initialize();
	}

	/**
	 * 全部消息
	 */
	public function messageListAll() {
		return $this->fetch();
	}
	
	/**
	 * 发布列表
	 */
	public function publishList() {
		return $this->fetch();
	}
	
}
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
// | 系统首页
// +----------------------------------------------------------------------
namespace app\system\controller;

use app\common\controller\AuthController;

/**
 * 管理首页面
 * @author oliver
 *
 */
class Index extends AuthController{
	
	public function _initialize(){
		//parent::_initialize();
	}

	/**
	 * 显示首页面
	 */
	public function index() {
		$menu = $this->getLoginMenu();
		$admin = $this->getLoginMenu();
		//$this->assign('admin',$admin);
		$this->assign('menu',$menu);
		return $this->fetch();
	}
	
}
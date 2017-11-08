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

namespace app\passport\controller;

use app\common\service\LoginSvc;
use app\common\controller\BaseController;

/**
 * 管理账号登录、登出
 * @author oliver
 *
 */
class Passport extends BaseController{

	/**
	 * 管理商户登录页面
	 */
	public function login() {
		if(LoginSvc::isLogin()){
			$this->redirect(config('website.page_index'));
		}else{
			return $this->fetch();
		}
	}
	/**
	 * 账号登录
	 */
	public function dologin(){
		$username = $this->request->post('username');
		$password = $this->request->post('password');
		$rtnData = LoginSvc::doLogin($username, $password);
		if ($rtnData['code']==1) {
			$this->success($rtnData['msg'],config('website.page_index'));
		}else{
			$this->error($rtnData['msg']);
		}
	}
	
	
	/**
	 * 账号注册
	 */
	public function reg(){
		if($this->isLogin()){
			$this->redirect(config('page_user'));
		}else{
			return $this->fetch();
		}
	}
	
	/**
	 * 管理退出
	 */
	public function logout(){
		LoginSvc::doLogout();
		return $this->fetch('login');
	}
	
}
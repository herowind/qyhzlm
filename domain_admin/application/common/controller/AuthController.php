<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------
namespace app\common\controller;

use app\common\service\LoginSvc;

use think\Controller;

/**
 * 授权控制器
 * @author oliver
 *
 */
class AuthController extends BaseController{
	protected $loginAdminId;
	
	/**
	 * 描述：全局初始化
	 */
	public function _initialize(){
		$this->loginAdminId = LoginSvc::isLogin();
		if($this->loginAdminId){
			$this->authCheck();//检查管理员菜单操作权限
		}else{
			$this->error('您尚未登录',url('passport/passport/login'),1);
		}
	}
	
	protected function getLoginAdmin(){
		return LoginSvc::getLoginUser();
	}
	
	protected function getLoginMenu(){
		return LoginSvc::getLoginMenu();
	}
	
	private function authCheck(){
		$loginAdmin = LoginSvc::getLoginUser();
// 		$ctl = CONTROLLER_NAME;
// 		$act = ACTION_NAME;
// 		$act_list = $loginAdmin['act_list'];
// 		//无需验证的操作
// 		//$uneed_check = array('login','logout','vertifyHandle','vertify','imageUp','upload','login_task');
// 		if($ctl == 'Index' || $act_list == 'all'){
// 			//后台首页控制器无需验证,超级管理员无需验证
// 			return true;
// 		}elseif(strpos($act,'ajax') || in_array($act,$uneed_check)){
// 			//所有ajax请求不需要验证权限
// 			return true;
// 		}else{
// 			$right = M('system_menu')->where("id", "in", $act_list)->cache(true)->getField('right',true);
// 			foreach ($right as $val){
// 				$role_right .= $val.',';
// 			}
// 			$role_right = explode(',', $role_right);
// 			//检查是否拥有此操作权限
// 			if(!in_array($ctl.'Controller@'.$act, $role_right)){
// 				$this->error('您没有操作权限,请联系超级管理员分配权限',U('Admin/Index/welcome'));
// 			}
// 		}
	}

}
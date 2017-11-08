<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------
namespace app\common\service;
use com\utils\FncCommon;

use com\utils\FncCrypt;

use think\Db;
use think\facade\App;

/***
 * 描述：登陆业务逻辑
* @since		2017-4-17
* @version		$Id$
*/
class LoginSvc{
	
	
	/**
	 * 描述：管理员登陆处理
	 * @param unknown_type $username
	 * @param unknown_type $password
	 * @return array
	 */
	public static function doLogin($username,$password){
		if (empty($username) || empty($password)) {
			return ['code'=>0,'msg'=>'账号或密码不能为空'];
		}
		$detail = Db::table('app_admin')->where('username',$username)->find();
		//验证密码
		if(empty($detail) || $detail['password'] != $password){
			return ['code'=>0,'msg'=>'账号或密码不正确'];
		}
		//验证状态
		if($detail['status'] != 1){
			return ['code'=>0,'msg'=>'您的账号已被关闭，请联系管理员'];
		}
		
		//session写入
		$isLogin = self::saveLogin($detail['id']);
		if($isLogin){
			return ['code'=>1,'msg'=>'登陆成功','data'=>$detail['id']];
		}else{
			return ['code'=>0,'msg'=>'系统异常，请稍后重试'];
		}
	}
	/**
	 * 描述：保存用户session
	 * @param unknown_type $id		
	 * @param unknown_type $isSys	系统模拟登陆
	 * @return boolean
	 */
	public static function saveLogin($id,$isSys=false){
		$detail = Db::table('app_admin','id,username,avatar,role_id')->where('id',$id)->find();
		if(!$isSys){
			//更新登录信息
			$data = [
			'last_login_time'	=> time(),
			'last_login_ip'		=> FncCommon::getClientIp(),
			];
			Db::table('app_admin')->where('id',$id)->update($data);
		}
		
		//读取权限信息
		$actList = Db::table('app_admin_role')->where('id',$detail['role_id'])->value('act_list');
		$auth = [
			'admin_id'			=> $id,
			'username'			=> $detail['username'],
			'avatar'			=> $detail['avatar'],
			'act_list'			=> $actList,
		];
		
		session(SES_ADMIN_AUTH, $auth);
		session(SES_ADMIN_AUTH_SIGN, FncCrypt::dataAuthSign($auth));
		return true;
	}
	
	/**
	 * 判断是否登陆
	 * @return number
	 */
	public static function isLogin(){
		$loginUser = session(SES_ADMIN_AUTH);
		if (empty($loginUser)) {
			return 0;
		} else {
			return session(SES_ADMIN_AUTH_SIGN) == FncCrypt::dataAuthSign($loginUser) ? $loginUser['admin_id'] : 0;
		}
	}
	
	/**
	 * 获得登陆用户信息
	 * @return Ambigous <mixed, void, boolean, \think\mixed, NULL, unknown, multitype:>
	 */
	public static function getLoginUser(){
		$loginUser = session(SES_ADMIN_AUTH);
		return $loginUser;
	}	
	
	/**
	 * 获得登陆用户菜单
	 * @return array
	 */
	public static function getLoginMenu(){
		$loginUser = session(SES_ADMIN_AUTH);
		$menuArr = include App::getConfigPath().'system/menu.php';
		return $menuArr;
		
	}
	
	public static function doLogout(){
		session(null);
	}
}

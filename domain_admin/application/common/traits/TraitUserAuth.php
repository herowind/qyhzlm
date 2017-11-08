<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------
namespace app\common\traits;

use com\utils\FncCrypt;

/**
 * 授权组件
 * @author oliver
 *
 */
trait TraitUserAuth {
	protected $loginUser = null;	//当前登录的用户
	/**
	 * 是否登陆 0：未登陆 >0 已登陆
	 * @return number
	 */
	public function isLogin(){
		$this->loginUser = session(SES_ADMIN_AUTH);
		if (empty($this->loginUser)) {
			return 0;
		} else {
			return session(SES_ADMIN_AUTH_SIGN) == FncCrypt::dataAuthSign($this->loginUser) ? $this->loginUser['uid'] : 0;
		}
	}
}
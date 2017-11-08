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
// | 小程序APP控制器
// +----------------------------------------------------------------------
namespace app\common\controller;

use app\core_api\service\Constants;

use app\core_api\service\LoginService;
use think\Request;
use think\facade\Log;

class AppController{
	// Request实例
	protected $request;	
	protected $user;
	protected function _initialize(){}
	
	/**
	 * 架构函数
	 * @param Request $request Request对象
	 * @access public
	 */
	public function __construct(Request $request){
		$this->request = $request;
		$header = $request->header();
		// 控制器初始化
		$this->_initialize();
	}
	
	protected function checkLogin(){
		$this->user = LoginService::checkwx($this->request->header());
//		$this->user['id'] = 1;
// 		$this->user['realname'] = '洪涛';
// 		$this->user['avatar'] = 'https://wx.qlogo.cn/mmopen/vi_32/FACQtw2ciaDIf6Vk5KKIZhT9pre33l9wXiayIiabcMMXYgGGoV3mObnB6xJibmYeKJGfW7BzaLZoTcvbIibGvowf9RA/0';
// 		$this->user['openid'] = 'oU6sL0X04RA3KGQW_2a1gGRs7-l4';
// 		Log::record('openid:'.$this->user['openid']);
		
		if(empty($this->user)){
			$rtnData[Constants::WX_SESSION_MAGIC_ID] 	= 1;
			$rtnData['code'] = 0;
			$rtnData['msg'] = '未登录';
			$rtnData['error'] = Constants::ERR_INVALID_SESSION;
			echo json_encode($rtnData,JSON_UNESCAPED_UNICODE) ;
			exit();
		}
	}
}
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

namespace app\common\controller;

use think\Controller;
/**
 * 基础控制器
 * @author oliver
 *
 */
class BaseController extends Controller{
	/**
	 * 初始化
	 */
	public function _initialize(){
	}
	
	/**
	 * 设置最后访问页面
	 */
	public function setLastUrl(){
		cookie('lasturl',strip_tags($_SERVER['REQUEST_URI']));
	}
	
	/**
	 * 提取最后访问页面
	 */
	public function getLastUrl(){
		$lastUrl = cookie('lasturl');
		if($lastUrl){
			return $lastUrl;
		}else{
			return $_SERVER['HTTP_HOST'];
		}
	}
}
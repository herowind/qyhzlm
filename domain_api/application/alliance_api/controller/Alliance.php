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
// | 联盟商家初始化
// +----------------------------------------------------------------------

namespace app\alliance_api\controller;

use app\common\controller\AppController;
use app\alliance_api\service\AppCacheSvc;

class Alliance extends AppController{
	//初始化
	public function _initialize(){
		parent::_initialize();
	}
	
	public function init(){
	    //$alliance['cateList'] = AppCacheSvc::getCateTree();
	    //mode=1 正常 mode =0 异常
	    $alliance['config'] = ['mode'=>0];
	    
	    return ['code'=>1,'data'=>$alliance];
	}
}
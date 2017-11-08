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
// | 预约服务：商务包车，长途拼车，空车配货
// +----------------------------------------------------------------------

namespace app\pintu\controller;

use app\common\controller\AuthController;

class Appt extends AuthController{
	
	public function _initialize(){
		//parent::_initialize();
	}
	
	/**
	 * 司机首页
	 */
	public function index() {
	    return $this->fetch();
	}

	/**
	 * 预约查询
	 */
	public function search() {
		return $this->fetch();
	}
	
	/**
	 * 添加预约
	 */
	public function add() {
		return $this->fetch();
	}
	
	/**
	 * 修改司机
	 */
	public function upd() {
	    return $this->fetch();
	}
	
	/**
	 * 删除司机，同时删除该司机下的所有车辆
	 * 注：存在订单的司机不可以删除
	 */
	public function del() {
	    return $this->fetch();
	}
	
}
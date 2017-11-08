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
// | 包车司机信息
// +----------------------------------------------------------------------

namespace app\pintu\controller;

use app\common\controller\AuthController;
use app\pintu\service\validate\PintuDriverValid;
use app\pintu\model\PintuDriver;
use app\pintu\model\PintuDriverCar;

class Driver extends AuthController{
	
	public function _initialize(){
		//parent::_initialize();
	}
	
	/**
	 * 发布中心
	 */
	public function index() {
	    return $this->fetch();
	}

	/**
	 * 查询包车单列表
	 */
	public function search() {
	    $driverMod = new PintuDriver();
	    $pageData = $driverMod->listPageBySearch();
	    $this->assign('pageData',$pageData);
		return $this->fetch();
	}
	
	/**
	 * 新建包车单
	 */
	public function add() {
		return $this->fetch();
	}
	
	public function pop_add(){
	    if($this->request->isPost()){
	        //验证司机账号
	        $data = $this->request->only(['mobile','password','realname']);
	        $validate = new PintuDriverValid();
	        $checkRst = $validate->check($data,'','add');
	        if($checkRst !== true){
	            //验证失败
	            $this->error($validate->getError());
	        }else{
	            //验证通过
	            $driverMod = new PintuDriver();
	            $num = $driverMod->data($data)->save();
	            if($num === 1){
	                $this->success('操作成功');
	            }else{
	                $this->error('操作失败');
	            } 
	        }
	    }else{
	        exit($this->fetch());
	    }
	    
	}
	
	public function upd_status(){
	    $data = $this->request->only(['id','status']);
	    $detail = PintuDriver::field('id,status')->find($data['id']);
	    if(isset($detail['id'])){
	        $detail->status = $data['status'];
	        $num = $detail->save();
	        if($num === 1){
	            $this->success('操作成功');
	        }else{
	            $this->success('数据无变化');
	        }
	    }else{
	        $this->error('数据不存在');
	    }
	}
	
	/**
	 * 修改包车单
	 */
	public function edit() {
	    $detail = PintuDriver::field('id,mobile,password,realname,call_num,status')->find($this->request->param('id'));
	    $detail->car_list = PintuDriverCar::where('driver_id',$this->request->param('id'))->select();
	    $this->assign('detail',$detail);
	    return $this->fetch();
	}
	
	/**
	 * 删除司机
	 */
	public function remove() {
	    $num = PintuDriver::destroy($this->request->param('id'));
        if($num !== false){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
	}
	
	/**
	 * 关闭预约订单：任何状态下的订单都可以关闭
	 */
	public function close() {
	    return $this->fetch();
	}
	
}
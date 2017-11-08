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
// | 车主车辆信息
// +----------------------------------------------------------------------

namespace app\pintu\controller;

use app\common\controller\AuthController;
use app\pintu\model\PintuDriverCar;
use app\pintu\service\validate\PintuDriverCarValid;

class DriverCar extends AuthController{
	
	public function _initialize(){
		//parent::_initialize();
	}

	/**
	 * 查询数据
	 */
	public function search() {
	    $driverCarMod = new PintuDriverCar();
	    $pageData = $driverCarMod->listPageBySearch();
	    $this->assign('pageData',$pageData);
		return $this->fetch('/driver/car/search');
	}
	
	/**
	 * 编辑数据
	 */
	public function pop_edit(){
	    if($this->request->isPost()){
	        //验证司机账号
	        $data = $this->request->only(['driver_id','type','num','buy_year','color','seat','remark']);
	        $validate = new PintuDriverCarValid();
	        $checkRst = $validate->check($data,'','add');
	        if($checkRst !== true){
	            //验证失败
	            $this->error($validate->getError());
	        }else{
	            //验证通过
	            if($this->request->param('id')){
	                //更新操作（先查询在更新）
	                $detail = PintuDriverCar::get($this->request->param('id'));
	                foreach ($data as $key=>$val){
	                    $detail->$key = $val;
	                }
	                $num = $detail->save();
	                if($num !== false){
	                    $this->success('操作成功');
	                }else{
	                    $this->error('操作失败');
	                }
	            }else{
	                //新增操作
	                $driverCarMod = new PintuDriverCar();
	                $num = $driverCarMod->data($data)->save();
	                if($num !== false){
	                    $this->success('操作成功');
	                }else{
	                    $this->error('操作失败');
	                }
	            }
	        }
	    }else{
	        if($this->request->param('id')){
	            $detail = PintuDriverCar::get($this->request->param('id'));
	            $this->assign('detail',$detail);
	        }else{
	            $detail = [
	                'driver_id' => $this->request->param('driver_id'),
	            ];
	            $this->assign('detail',$detail);
	        }
	        
	        exit($this->fetch('/driver/car/pop_edit'));
	    }
	    
	}
	
	/**
	 * 删除车辆
	 */
	public function remove() {
	    $num = PintuDriverCar::destroy($this->request->param('id'));
        if($num !== false){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
	}
}
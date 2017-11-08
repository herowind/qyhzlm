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
// | 客户预约单
// +----------------------------------------------------------------------
namespace app\pintu_api\controller;
use app\common\controller\AppController;
use app\core\service\UploadSvc;
use app\pintu_api\model\PintuAppt;

class UserAppt extends AppController{
	//初始化
	public function _initialize(){
		parent::_initialize();
	}
	
	public function save(){
	    $data = $this->request->param();
		switch ($this->request->param('type')){
		    case 1:
		        return $this->saveByday();
		        break;
		    case 2:
		        return $this->saveBycity();
		        break;
		    default:
		        return ['code'=>0,'msg'=>'预约不成功'];
		}
	}
	
	public function detail(){
	    $detail = PintuAppt::get($this->request->param('id'));
	    return ['code'=>0,'msg'=>'操作成功','data'=>$detail];
	}
	
	public function listpage(){
	    $list = PintuAppt::all();
	    return ['code'=>0,'msg'=>'操作成功','data'=>$list];
	}
	
	private function saveByday(){
        $data = $this->request->only(['type','from_city_name','from_city_addr','from_loc_la','from_loc_lo','cust_date','cust_time','cust_days','cust_num','cust_mobile','cust_remark']);
        $detail = PintuAppt::create($data);
        if($detail!==false){
            return ['code'=>1,'msg'=>'预约不成功','data'=>$detail->id];
        }else{
            return ['code'=>0,'msg'=>'预约失败'];
        }
	}
	
	private function saveBycity(){
	    $data = $this->request->only(['type','from_city_name','from_city_addr','from_loc_la','from_loc_lo','to_city_name','to_city_addr','to_loc_la','to_loc_lo','cust_date','cust_time','cust_num','cust_mobile','cust_remark']);
	    $detail = PintuAppt::create($data);
	    if($detail!==false){
	        return ['code'=>1,'msg'=>'预约不成功','data'=>$detail->id];
	    }else{
	        return ['code'=>0,'msg'=>'预约失败'];
	    }
	}
}
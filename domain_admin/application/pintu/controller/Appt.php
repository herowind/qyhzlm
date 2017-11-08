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
use app\pintu\service\validate\PintuApptValid;
use app\pintu\model\PintuAppt;

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
	    $apptMod = new PintuAppt();
	    $pageData = $apptMod->listPageBySearch($this->request->param());
	    $this->assign('pageData',$pageData);
		return $this->fetch();
	}
	
	public function add_day(){
	    $this->redirect('edit',['type'=>1]);
	}
	
	public function add_city(){
	    $this->redirect('edit',['type'=>2]);
	}
	
	/**
	 * 添加预约
	 */
	public function edit() {
        if($this->request->isPost()){
	        //验证
	        $data = $this->request->post();
	        $validate = new PintuApptValid();
	        $checkRst = $validate->check($data,'','type_'.$data['type']);
	        if($checkRst !== true){
	            //验证失败
	            $this->error($validate->getError());
	        }else{
	            //验证通过
	            if($this->request->param('id')){
	                //更新操作（先查询在更新）
	                $detail = PintuAppt::get($this->request->param('id'));
	                $num = $detail->save($data);
	                if($num !== false){
	                    $this->success('操作成功');
	                }else{
	                    $this->error('操作失败');
	                }
	            }else{
	                //新增操作
	                $apptMod = new PintuAppt();
	                $num = $apptMod->save($data);
	                if($num !== false){
	                    $this->success('操作成功');
	                }else{
	                    $this->error('操作失败');
	                }
	            }
	        }
	    }else{
	        if($this->request->param('id')){
	            $detail = PintuAppt::get($this->request->param('id'));
	            $this->assign('detail',$detail);
	        }else{
	            $detail = [
	                'type' => $this->request->param('type',1),
	            ];
	            $this->assign('detail',$detail);
	        }
	        
	        return $this->fetch('edit');
	    }
	}
	
	/**
	 * 修改司机
	 */
	public function appt_day() {
	    exit($this->fetch()) ;
	}
	
	/**
	 * 删除预约单
	 */
	public function remove() {
	    $num = PintuAppt::destroy($this->request->param('id'));
        if($num !== false){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
	}
	
	public function status_toggle(){
	    $data = $this->request->only(['id','field']);
	    $detail = PintuAppt::get($this->request->param('id'));
	    if($detail[$data['field']] === 1){
	        $num = $detail->data([$data['field']=>0])->save();
	    }else{
	        $num = $detail->data([$data['field']=>1])->save();
	    }
	    if($num !== false){
	        $this->success('操作成功','',$detail[$data['field']]);
	    }else{
	        $this->error('操作失败','',$detail[$data['field']]);
	    }
	}
	
}
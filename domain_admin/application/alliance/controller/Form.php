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
// | 动态表单
// +----------------------------------------------------------------------

namespace app\alliance\controller;

use app\common\controller\AuthController;
use app\alliance\model\AllianceForm;
use think\Validate;

class Form extends AuthController{
	
	public function _initialize(){
		//parent::_initialize();
	}
	
	/**
	 * 表单列表
	 */
	public function search() {
	    $formMod = new AllianceForm();
	    $pageData = $formMod->listPageBySearch();
	    $this->assign('pageData',$pageData);
	    return $this->fetch();
	}
	
	/**
	 * 编辑表单
	 */
	public function pop_edit() {
        if($this->request->isPost()){
	        //验证
	        $data = $this->request->post();
	        $validate = Validate::make(
	           [
	               'title'             => 'require',
	           ],
	           [
	               'title.require'     => '表单名称不能为空',
	           ]
	        );
	                            
	        $checkRst = $validate->check($data);
	        if($checkRst !== true){
	            //验证失败
	            $this->error($validate->getError());
	        }else{
	            //验证通过
	            if($this->request->param('id')){
	                //更新操作（先查询在更新）
	                $detail = AllianceForm::get($this->request->param('id'));
	                $num = $detail->save($data);
	                if($num !== false){
	                    $this->success('操作成功');
	                }else{
	                    $this->error('操作失败');
	                }
	            }else{
	                //新增操作
	                $num = AllianceForm::create($data);
	                if($num !== false){
	                    $this->success('操作成功');
	                }else{
	                    $this->error('操作失败');
	                }
	            }
	        }
	    }else{
	        if($this->request->param('id')){
	            $detail = AllianceForm::get($this->request->param('id'));
	            $this->assign('detail',$detail);
	        }
	        exit($this->fetch('pop_edit'));
	    }
	}
	
	/**
	 * 字段查询
	 */
	public function field_list() {
	    $formMod = new AllianceForm();
	    $form = $formMod->field('id,title')->find($this->request->param('pid'));
	    $form['field_list'] = $formMod->where('pid',$this->request->param('pid'))->order('sort desc')->select();
	    $this->assign('form',$form);
	    return $this->fetch();
	}
	
	/**
	 * 编辑表单
	 */
	public function field_edit() {
	    if($this->request->isPost()){
	        //验证
	        $data = $this->request->post();
	        $validate = Validate::make(
	            [
	                'title'             => 'require',
	            ],
	            [
	                'title.require'     => '字段名称不能为空',
	            ]
	            );
	         
	        $checkRst = $validate->check($data);
	        if($checkRst !== true){
	            //验证失败
	            $this->error($validate->getError());
	        }else{
	            //验证通过
	            if($this->request->param('id')){
	                //更新操作（先查询在更新）
	                $detail = AllianceForm::get($this->request->param('id'));
	                $num = $detail->save($data);
	                if($num !== false){
	                    $this->success('操作成功');
	                }else{
	                    $this->error('操作失败');
	                }
	            }else{
	                //新增操作
	                $num = AllianceForm::create($data);
	                if($num !== false){
	                    $this->success('操作成功');
	                }else{
	                    $this->error('操作失败');
	                }
	            }
	        }
	    }else{
	        if($this->request->param('id')){
	            $detail = AllianceForm::get($this->request->param('id'));
	            $this->assign('detail',$detail);
	        }else{
	            $detail['pid'] = $this->request->param('pid');
	            $this->assign('detail',$detail);
	        }
	        exit($this->fetch('field_edit'));
	    }
	}	

	/**
	 * 删除表单或字段
	 */
	public function remove(){
	    $num = AllianceForm::destroy($this->request->param('id'));
	    if($num !== false){
	        $this->success('操作成功');
	    }else{
	        $this->error('操作失败');
	    }
	}
	
	/**
	 * 编辑字段
	 */
	public function sort_edit() {
	    $detail = AllianceForm::get($this->request->param('id'));
	    $num = $detail->save($this->request->only('sort'));
        if($num !== false){
            $this->success('操作成功');
        }else{
            $this->error('操作失败');
        }
	}
	
	
	
	/**
	 * 更改状态信息
	 */
	public function status_toggle(){
	    $data = $this->request->only(['id','field']);
	    $detail = AllianceForm::get($this->request->param('id'));
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
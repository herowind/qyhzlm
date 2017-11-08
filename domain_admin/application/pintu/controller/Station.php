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
// | 站点控制器
// +----------------------------------------------------------------------

namespace app\pintu\controller;

use app\common\controller\AuthController;
use app\pintu\model\PintuStation;
use think\Validate;
use think\validate\ValidateRule;

class Station extends AuthController{
	
	public function _initialize(){
		//parent::_initialize();
	}

	/**
	 * 数据查询
	 */
	public function search() {
	    $stationMod = new PintuStation();
	    $pageData = $stationMod->listPageByPid($this->request->param('pid',0));
	    $this->assign('pageData',$pageData);
		return $this->fetch();
	}
	
	/**
	 * 编辑数据
	 */
	public function pop_edit(){
	    if($this->request->isPost()){
	        //验证站点信息
	        $data = $this->request->only(['name']);
	        $checkRst = Validate::make()
	                            ->rule([
	                                       'name'   => ValidateRule::must()->isChs()->title('站点名称'),
	                                  ])
	                            ->check($data);
	        if($checkRst !== true){
	            //验证失败
	            $this->error($checkRst->getError());
	        }else{
	            //验证通过
	            if($this->request->param('id')){
	                //更新操作（先查询在更新）
	                $detail = PintuStation::get($this->request->param('id'));
	                foreach ($data as $key=>$val){
	                    $detail->$key = $val;
	                }
	                $detail['name_pinyin'] = $detail['name'];
	                $detail['name_quick'] = $detail['name'];
	                $num = $detail->save();
	                if($num !== false){
	                    $this->success('操作成功');
	                }else{
	                    $this->error('操作失败');
	                }
	            }else{
	                //新增操作
	                $driverCarMod = new PintuStation();
	                $data['name_pinyin'] = $data['name'];
	                $data['name_quick'] = $data['name'];
	                $num = $driverCarMod->data($data,true)->save();
	                if($num !== false){
	                    $this->success('操作成功');
	                }else{
	                    $this->error('操作失败');
	                }
	            }
	        }
	    }else{
	        if($this->request->param('id')){
	            $detail = PintuStation::get($this->request->param('id'));
	            $this->assign('detail',$detail);
	        }else{
	            $detail = [
	                'pid' => 0,
	            ];
	            $this->assign('detail',$detail);
	        }
	        
	        exit($this->fetch('/station/pop_edit'));
	    }
	    
	}
	
	/**
	 * 删除车辆
	 */
	public function remove() {
	    $num = PintuStation::destroy($this->request->param('id'));
	    if($num !== false){
	        $this->success('操作成功');
	    }else{
	        $this->error('操作失败');
	    }
	}
	
	public function status_toggle(){
	    $data = $this->request->only(['id','field']);
	    $detail = PintuStation::get($this->request->param('id'));
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
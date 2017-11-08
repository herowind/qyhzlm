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
// | 数据分类
// +----------------------------------------------------------------------

namespace app\alliance\controller;

use app\common\controller\AuthController;
use app\alliance\model\AllianceCate;
use think\Validate;

class Cate extends AuthController{
	
	public function _initialize(){
		//parent::_initialize();
	}
	
	/**
	 * 查询分类
	 */
	public function search() {
	    $pid = $this->request->param('pid',0);
	    $list = AllianceCate::where('pid',$pid)->order('sort desc')->select();
	    if($pid){
	        $pdetail = AllianceCate::get($pid);
	    }else{
	        $pdetail['title'] = '顶级';
	    }
	    $this->assign('list',$list);
	    $this->assign('pdetail',$pdetail);
	    return $this->fetch();
	}
	
	/**
	 * 编辑分类
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
	               'title.require'     => '分类名称不能为空',
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
	                $detail = AllianceCate::get($this->request->param('id'));
	                $data['bind_form'] = null;
	                $data['bind_attribute'] = null;
	                $num = $detail->save($data);
	                if($num !== false){
	                    $this->success('操作成功');
	                }else{
	                    $this->error('操作失败');
	                }
	            }else{
	                //新增操作
	                $data['bind_form'] = null;
	                $data['bind_attribute'] = null;
	                $num = AllianceCate::create($data);
	                if($num !== false){
	                    $this->success('操作成功');
	                }else{
	                    $this->error('操作失败');
	                }
	            }
	        }
	    }else{
	        if($this->request->param('id')){
	            //编辑页面
	            $detail = AllianceCate::get($this->request->param('id'));
	            $this->assign('detail',$detail);
	        }else{
	            //新增页面
	            $detail['pid'] = $this->request->param('pid',0);
	            $this->assign('detail',$detail);
	        }
	        exit($this->fetch('pop_edit'));
	    }
	}

	/**
	 * 删除分类
	 */
	public function remove(){
	    $num = AllianceCate::destroy($this->request->param('id'));
	    if($num !== false){
	        $this->success('操作成功');
	    }else{
	        $this->error('操作失败');
	    }
	}
	
	/**
	 * 编辑排序
	 */
	public function sort_edit() {
	    $detail = AllianceCate::get($this->request->param('id'));
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
	    $detail = AllianceCate::get($this->request->param('id'));
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
	
	/**
	 * 上传图标
	 */
	public function icon_upload(){
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = $this->request->file('imgFile');
	    // 移动到框架应用根目录/uploads/ 目录下
	    $info = $file->move( '../../uploads/alliance/cate',request()->param('id'));
	    if($info){
	        $url = 'http://upload.local.qyhzlm.com/alliance/cate/'.$info->getSaveName();
	        $detail = AllianceCate::get($this->request->param('id'));
	        $num = $detail->data(['icon'=>$url])->save();
	        if($num !== false){
	           // $this->success('操作成功','',$url);
	            
	            exit(json_encode(['error'=>0,'url'=>$url]));
	        }else{
	             exit(json_encode(['error'=>1,'message'=>'上传失败']));
	        }
	    }else{
	        // 上传失败获取错误信息
	        exit(json_encode(['error'=>1,'message'=>$file->getError()]));
	    }
	}
	
}
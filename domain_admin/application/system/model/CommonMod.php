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
// | 核心公共模型
// +----------------------------------------------------------------------

namespace app\system\model;

use think\Db;

class CommonMod{
	protected $tableName = '';
	protected $fieldNames = [];
	protected $editData = [];
	protected $where = [];
	
	/**
	 * 查询详细
	 * @param unknown_type $id
	 * @return unknown
	 */
	public function detail($id,$field='*'){
		$detail = Db::table($this->tableName)->field($field)->find($id);
		return $detail;
	}
	
	/**
	 * 新增一条数据
	 * @param array $data
	 * @return unknown
	 */
	public function add($data,$editData=[]){
		//数据准备
		$this->editData = $editData;
		$this->editData['update_time'] = time();
		$this->editData['create_time'] = time();
		
		$this->setEditData($data);
		$id = Db::table($this->tableName)->insertGetId($this->editData);
		if($id){
			return ['code'=>1,'msg'=>'添加成功','data'=>$id];
		}else{
			return ['code'=>0,'msg'=>'添加失败'];
		}
	}
	
	/**
	 * 编辑一条数据
	 * @param array $data
	 * @return unknown
	 */
	public function edit($data,$editData=[]){
		//数据准备
		$this->editData = $editData;
		$this->editData['update_time'] = time();

		$this->setEditData($data);
		$numRows = Db::table($this->tableName)->where('id',$data['id'])->update($this->editData);
		if($numRows===1){
			return ['code'=>1,'msg'=>'编辑成功','data'=>$data['id']];
		}else{
			return ['code'=>0,'msg'=>'编辑失败'];
		}
	}
	
	/**
	 * 删除一条数据
	 * @param array $data
	 * @return unknown
	 */
	public function remove($id,$where=[]){
		$where['id'] = $id;
		$numRows = Db::table($this->tableName)->where($where)->delete();
		if($numRows===1){
			return ['code'=>1,'msg'=>'删除成功'];
		}else{
			return ['code'=>0,'msg'=>'信息不存在'];
		}
	}

	/**
	 * 封装新增或编辑的数据
	 * @param array $data
	 * @return unknown
	 */
	protected function setEditData($data){
		foreach ($this->fieldNames as $val){
			if(isset($data[$val])){
				$this->editData[$val] = $data[$val];
			}
		}
		return $this->editData;
	}
	
	/**
	 * 描述：设置搜索数据
	 */
	protected function setWhere($search,$fieldName){
		//判断字段是否设置
		if(isset($search[$fieldName]) && $search[$fieldName]!=''){
			$this->where[$fieldName] = $search[$fieldName];
		}
	}
	
	/**
	 * 描述：设置模糊搜索字段
	 */
	protected function setWhereKeywords($search,$fieldLike,$filedKey='keywords'){
		if(isset($search[$filedKey]) && !empty($search[$filedKey])){
			$this->where[$fieldLike] = ['like',"%{$search[$filedKey]}%"];
		}
	}
	
	public function getTableName(){
		return $this->tableName;
	}
}
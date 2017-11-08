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
// | 分类模型
// +----------------------------------------------------------------------

namespace app\alliance\widget;
use app\alliance\model\AllianceAttribute;

/**
 * 系统字典选择器
 */
class AttributeWidget{
	public function attributeOptions($selected=''){
		$options = '';
		$list = AllianceAttribute::all(['pid'=>0]);
		foreach ($list as $val){
			if($val['id'] != $selected){
				$options.='<option value="';
			}else{
				$options.='<option selected value="';
			}
			$options.=$val['id'];
			$options.='">';
			$options.=$val['title'];
			$options.='</option>';
		}
		return $options;
	}
}
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
// | 联盟商家初始化
// +----------------------------------------------------------------------

namespace app\alliance_api\controller;

use app\common\controller\AppController;
use app\alliance_api\model\AllianceInfo;
use think\image\Image;

class Info extends AppController{
	//初始化
	public function _initialize(){
		parent::_initialize();
	}
	
	public function save(){
	    $data = $this->request->param();
	    if(!empty($data['pics'])){
	        $pics = [];
	        foreach ($data['pics'] as $val){
	            $pics[] = ['o'=>$val,'t'=>substr_replace($val, '_thumb', strripos($val,'.'),0)];
	        }
	        $data['pics'] = json_encode($pics);
	    }
		$detail = AllianceInfo::create($data);
		if($detail!==false){
		    return ['code'=>1,'msg'=>'发布成功','data'=>$detail->id];
		}else{
		    return ['code'=>0,'msg'=>'发布失败'];
		}
	}
	
	public function detail(){
	    $detail = AllianceInfo::get($this->request->param('id'));
	    return ['code'=>0,'msg'=>'查询成功','data'=>$detail];
	}
	
	public function getInfoList(){
	    $infoMod = new AllianceInfo();
	    $listPage = $infoMod->getListPage($this->request->param());
	    return ['code'=>1,'msg'=>'查询成功','data'=>$listPage];
	}
	
	private function saveByday(){
        $data = $this->request->only(['user_mobile','from_city_name','from_city_addr','from_loc_la','from_loc_lo','cust_date','cust_time','cust_days','cust_num','cust_mobile','cust_remark']);
        $detail = AllianceInfo::create($data);
        if($detail!==false){
            return ['code'=>1,'msg'=>'预约不成功','data'=>$detail->id];
        }else{
            return ['code'=>0,'msg'=>'预约失败'];
        }
	}
	
	/**
	 * 上传图标
	 */
	public function uploadImg(){
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = $this->request->file('imgFile');
	    // 移动到框架应用根目录/uploads/ 目录下
	    $info = $file->move( 'G:/www/qyhzlm/uploads/alliance/info');
	    if($info){
	        $url = 'http://upload.local.qyhzlm.com/alliance/info/'.$info->getSaveName();
	         
	        $image = Image::open('G:/www/qyhzlm/uploads/alliance/info/'.$info->getSaveName());
	        $thumb = substr_replace($info->getSaveName(), '_thumb', strripos($info->getSaveName(),'.'),0);
	        $image->thumb(200, 200,3)->save('G:/www/qyhzlm/uploads/alliance/info/'.$thumb);
	        $url_thumb = 'http://upload.local.qyhzlm.com/alliance/info/'.$thumb;
	        return ['code'=>1,'msg'=>'上传成功','url'=>$url];
	    }else{
	        // 上传失败获取错误信息
	        return ['code'=>0,'msg'=>$file->getError()];
	    }
	}
}
<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------
namespace app\core\controller;

use think\Log;

use app\core_api\service\UploadSvc;

/**
 * 管理首页面
 * @author oliver
 *
 */
class Fileapi{
	/**
	 * 显示首页面
	 */
	public function uploadImage() {
		
		
		Log::record("=====================");
		$vars = $_POST;
		foreach ($_FILES['logo'] as $val=>$key){
			Log::record($val.":".$key);
		}
		$file = request()->file('logo');
		$filePath = UploadSvc::uploadFile($file,$_POST['subDir']);
		
		if($filePath){
			return ['code'=>1,'msg'=>"上传成功",'url'=>$filePath];
		}else{
			return ['code'=>0,'msg'=>"上传失败",'url'=>''];
		}
	}
}
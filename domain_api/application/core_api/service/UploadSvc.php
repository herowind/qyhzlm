<?php
namespace app\core_api\service;

use think\image\Image;

/***
* 描述：上传功能
* @author		wanght <whtaok@gmail.com>
* @since		2014-2-17
* @version		$Id$
*/
class UploadSvc{
	/**
	 * 
	 * @param unknown $file
	 * @param string $subDir
	 * @param string $onlyThumb
	 * @return number[]|string[]|number[]|NULL[]
	 */
	public static function uploadFile($file,$subDir='common',$onlyThumb=false){
	    // 移动到框架应用根目录/uploads/ 目录下
	    $uploadPath = config('app.website.upload_path').'/'.$subDir.'/';
	    $uploadUrl = config('app.website.upload_url').'/'.$subDir.'/';
	    $info = $file->move($uploadPath);
	    if($info){
	        
	        //上传原图
	        $image = Image::open($uploadPath.$info->getSaveName());
	        $url = $uploadUrl.$info->getSaveName();
	        
	        //生成缩略图
	        if($onlyThumb){
	            //覆盖原图
	            $thumbName = $info->getSaveName();
	        }else{
	            $thumbName = substr_replace($info->getSaveName(), '_thumb', strripos($info->getSaveName(),'.'),0);
	        }
	        $image->thumb(200, 200,3)->save($uploadPath.$thumbName);
	        $thumbUrl = $uploadUrl.$thumbName;
	        
	        return ['code'=>1,'msg'=>'上传成功','url'=>['o'=>$url,'t'=>$thumbUrl]];
	    }else{
	        // 上传失败获取错误信息
	        return ['code'=>0,'msg'=>$file->getError()];
	    }
	}
    
}

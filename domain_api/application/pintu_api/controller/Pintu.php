<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author=> 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------
namespace app\mod_city_app\controller;
use app\core\service\UploadSvc;

use think\Log;

use app\mod_city\service\member\CitySvc;
use com\utils\FncTree;
/**
 * 描述：城市API
 * @author oliver
 *
 */
class Pintu extends AppController{
	//初始化
	public function _initialize(){
		parent::_initialize();
	}
	
	public function getCityList(){
		$list = CitySvc::getCityList($this->getAgentUid());
		return $list ;
	}
	
	public function chooseCity(){
		//加载城市基本信息
		$city['city'] = CitySvc::cityInfo($this->getCityId());
		$city['city']['host'] = "edu.local.dlmicroworld.com";
		//加载店铺分类
		$listCate = CitySvc::getShopCateListByCityId($this->getCityId(),$this->getAgentUid());
		$city['dict']['shop_cate_tree'] = FncTree::listToTreeNoKey($listCate);
		
		//加载店铺分类
		$listArea = CitySvc::getCityAreaListByCityId($this->getCityId());
		$city['dict']['city_area_tree'] = FncTree::listToTreeNoKey($listArea);
		
		//加载信息分类
		$listInfo = CitySvc::getInfoCateListByCityId($this->getCityId(),$this->getAgentUid());
		$city['dict']['info_cate_tree'] = $listInfo;
		
		return $city;
	}
	
	public function index(){
		//加载幻首页信息
		$advList = CitySvc::getPublishByCityId($this->getCityId(),[1,2,3,4]);
		foreach ($advList as $val){
			switch($val['type']){
				case 1:
					$index['adv_list']['banner'][] = $val;
					break;
				case 2:
					$index['adv_list']['notice'][] = $val;
					break;
				case 3:
					$index['adv_list']['center'][] = $val;
					break;
				case 4:
					$index['adv_list']['footer'][] = $val;
					break;
			}
		}
		$index['status_list']['shop_cate'] = false;
		return ['code'=>1,'msg'=>'加载完毕','data'=>$index];
	}

	
	public function uploadImg(){
		$file = $this->request->file('img');
		$filePath = UploadSvc::uploadFile($file,$this->getCityId());
		
		if($filePath){
			return ['code'=>1,'msg'=>"上传成功",'url'=>$filePath];
		}else{
			return ['code'=>0,'msg'=>"上传失败",'url'=>''];
		}
	}
}
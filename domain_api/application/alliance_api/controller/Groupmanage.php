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
// | 社群通讯录控制器
// +----------------------------------------------------------------------

namespace app\alliance_api\controller;

use app\common\controller\AppController;
use app\alliance_api\model\AllianceGroup;
use app\alliance_api\model\AllianceGroupmember;
use app\core_api\service\UploadSvc;

class Groupmanage extends AppController{
	// 初始化
	public function _initialize(){
		parent::_initialize();
		$this->checkLogin();
	}
	
	/**
	 *  群详细
	 */
	public function groupDetail(){
	    $groupmemberMod = new AllianceGroupmember();
	    $listData = $groupmemberMod->listPageByUid($this->user['id']);
	    return ['code'=>1,'msg'=>'操作成功','data'=>$listData];
	}
	
	/**
	 *  创建/编辑
	 */
	public function groupSave(){
	    $data = $this->request->param();
	    if($data['memberfeetype'] == 1){
	        $data['memberfee'] = 0;
	    }
	    if($data['id']){
	        $detail = AllianceGroup::get($data['id']);
	        $detail->save($data);
	    }else{
	        $data['uid'] = $this->user['id'];
	        $data['createuid'] = $this->user['id'];
	        $data['createrealname'] = $this->user['realname'];
	        $detail = AllianceGroup::create($data);
	        if($detail!==false){
	            $groupCreator = [
	                'gid'=>$detail->id,
	                'uid'=>$detail->uid,
	                'isadmin'=>1,
	                'iscreator'=>1
	            ];
	            AllianceGroupmember::create($groupCreator);
	        }
	    }
	    if($detail!==false){
	        return ['code'=>1,'msg'=>'操作成功','data'=>$detail->id];
	    }else{
	        return ['code'=>0,'msg'=>'操作失败'];
	    }
	}
	
	/**
	 * 上传图标
	 */
	public function groupUploadLogo(){
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = $this->request->file('imgFile');
	    $rtnData = UploadSvc::uploadFile($file,'group',true);
	    return $rtnData;
	}
	
	/**
	 *  成员查询
	 */
	public function memberSearch(){
	   $params = $this->request->param();
	   $member = new AllianceGroupmember();
	   $pageData = $member->search($params['gid'],$params,$params['total']);
	   return ['code'=>1,'msg'=>'查询成功','data'=>$pageData];
	}
	
	/**
	 *  成员禁言
	 */
	public function memberBan(){
        $gid = $this->request->param('gid');
        $touid = $this->request->param('touid');
        $bantime = $this->request->param('bantime',365);
        switch($bantime){
            case 1:
                $bantime = strtotime("+1 day");
                break;
            case 3:
                $bantime = strtotime("+3 day");
                break;
            case 7:
                $bantime = strtotime("+7 day");  
                break;
            default:
                $bantime = strtotime("+365 day");
                break;
        }

        if($this->user['id'] == $touid){
            return ['code'=>0,'msg'=>'操作无效'];
        }
        //检查操作人权限
        $admin = AllianceGroupmember::get(['gid'=>$gid,'uid'=>$this->user['id']]);
        $touser = AllianceGroupmember::get(['gid'=>$gid,'uid'=>$touid]);
        if($admin && $admin['isadmin']){//必须是管理员权限
            if($touser['iscreator']){
                return ['code'=>0,'msg'=>'不能禁言群主'];
            }
            $count = $touser->save(['bantime'=>$bantime]);
            if($count == 1){
                return ['code'=>1,'msg'=>'操作成功'];
            }else{
                return ['code'=>0,'msg'=>'操作失败'];
            }
        }
	}
	
	/**
	 *  成员踢出
	 */
	public function memberKick(){
	    $gid = $this->request->param('gid');
	    $touid = $this->request->param('touid');

	    if($this->user['id'] == $touid){
	        return ['code'=>0,'msg'=>'操作无效'];
	    }
	    //检查操作人权限
	    $admin = AllianceGroupmember::get(['gid'=>$gid,'uid'=>$this->user['id']]);
        $touser = AllianceGroupmember::get(['gid'=>$gid,'uid'=>$touid]);   
	    if($admin && $admin['isadmin']){//必须是管理员权限
	        if($touser['iscreator']){
	            return ['code'=>0,'msg'=>'不能踢出群主'];
	        }
	        $count = $touser->delete(true);
	        if($count == 1){
	            return ['code'=>1,'msg'=>'操作成功'];
	        }else{
	            return ['code'=>0,'msg'=>'操作失败'];
	        }
	    }
	    
	}
	
	/**
	 *  成员设置/取消管理
	 */
	public function memberSetAdmin(){
	    $gid = $this->request->param('gid');
	    $touid = $this->request->param('touid');
	    $isadmin = $this->request->param('isadmin');
	    if($isadmin != 1){
	        $isadmin = 0;
	    }
	    if($this->user['id'] == $touid){
	        return ['code'=>0,'msg'=>'操作无效'];
	    }
	    //检查操作人权限
	    $group = AllianceGroup::get(['id'=>$gid]);
	    if($group['uid'] == $this->user['id']){//必须是群主权限
	       $count = AllianceGroupmember::where(['gid'=>$gid,'uid'=>$touid])->update(['isadmin'=>$isadmin]);
            if($count == 1){
                return ['code'=>1,'msg'=>'操作成功'];
            }else{
                return ['code'=>0,'msg'=>'操作失败'];
            }
	    }
	}
	
	/**
	 *  群主转让
	 */
	public function memberSetCreator(){
	    $gid = $this->request->param('gid');
	    $touid = $this->request->param('touid');
	    if($this->user['id'] == $touid){
	        return ['code'=>0,'msg'=>'操作无效'];
	    }
	    //转让人必须是群管理
	    $admin = AllianceGroupmember::get(['gid'=>$gid,'uid'=>$touid]);
	    if($admin && $admin['isadmin']){
	        $group = AllianceGroup::get(['id'=>$gid]);
	        if($group['uid'] == $this->user['id']){//必须是群主权限
	            $count = $group->save(['uid'=>$touid],['id'=>$gid]);
	            if($count !== false){
	                return ['code'=>1,'msg'=>'操作成功'];
	            }else{
	                return ['code'=>0,'msg'=>'操作失败'];
	            }
	        }else{
	            return ['code'=>0,'msg'=>'操作无效'];
	        }
	    }else{
	        return ['code'=>0,'msg'=>'转让人必须是群管理,请先设置该人为管理员'];
	    }
	    
	}
	
	
}
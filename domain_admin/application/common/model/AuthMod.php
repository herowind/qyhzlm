<?php
// +----------------------------------------------------------------------
// | 微世界 [ 微于形 精于心 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.dlmicroworld.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 大水 <2244115959@qq.com>
// +----------------------------------------------------------------------
namespace app\common\model;

use app\common\lib\utils\FncCommon;

use think\Db;

/**
 * 管理账号登录、登出
 * @author oliver
 *
 */
class AuthMod{
	//配置文件中必须要配置
	protected $_config = [];
	
	public function __construct($auth_config=[]){
		if(empty($auth_config)){
			exception('授权失败','1001');
		}
		$this->_config = $auth_config;
	}
	
	/**
	 * 检查权限
	 * @param name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
	 * @param uid  int           认证用户的id
	 * @param string mode        执行check的模式
	 * @param relation string    如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
	 * @return boolean           通过验证返回true;失败返回false
	 */
	public function check($name, $uid, $type = 1, $mode = 'url', $relation = 'or')
	{
		if (!$this->_config['AUTH_ON']) {
			return true;
		}
	
		$authList = $this->getAuthList($uid, $type); //获取用户需要验证的所有有效规则列表
		if (is_string($name)) {
			$name = strtolower($name);
			if (strpos($name, ',') !== false) {
				$name = explode(',', $name);
			} else {
				$name = array($name);
			}
		}
		$list = array(); //保存验证通过的规则名
		if ('url' == $mode) {
			$REQUEST = unserialize(strtolower(serialize($_REQUEST)));
		}
		foreach ($authList as $auth) {
			$query = preg_replace('/^.+\?/U', '', $auth);
			if ('url' == $mode && $query != $auth) {
				parse_str($query, $param); //解析规则中的param
				$intersect = array_intersect_assoc($REQUEST, $param);
				$auth      = preg_replace('/\?.*$/U', '', $auth);
				if (in_array($auth, $name) && $intersect == $param) {
					//如果节点相符且url参数满足
					$list[] = $auth;
				}
			} else if (in_array($auth, $name)) {
				$list[] = $auth;
			}
		}
		if ('or' == $relation and !empty($list)) {
			return true;
		}
		$diff = array_diff($name, $list);
		if ('and' == $relation and empty($diff)) {
			return true;
		}
		return false;
	}
	
	public function getMenuList($uid, $type=1){
		//读取用户所属用户组
		$groups = $this->getGroups($uid);
		$ids    = array(); //保存用户所属用户组设置的所有权限规则id
		foreach ($groups as $g) {
			$ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
		}
		$ids = array_unique($ids);
		if (empty($ids)) {
			return [];
		}
	
		$map = [
				'id'     => array('in', $ids),
				'type'   => $type,
		];
		//读取用户组所有权限规则
		$menuList = Db::table($this->_config['AUTH_RULE'])->where($map)->order('sort')->column('id,name,title,css,pid,levels,sort,status');
		//$menuTree = FncCommon::listToTree($menuList);
		return $menuList;
	}
	
	/**
	 * 根据用户id获取用户组,返回值为数组
	 * @param  uid int     用户id
	 * @return array       用户所属的用户组 array(
	 *     array('uid'=>'用户id','group_id'=>'用户组id','title'=>'用户组名称','rules'=>'用户组拥有的规则id,多个,号隔开'),
	 *     ...)
	 */
	public function getGroups($uid)
	{
		static $groups = array();
		if (isset($groups[$uid])) {
			return $groups[$uid];
		}
// 		$user_groups = Db::table($this->_config['AUTH_GROUP_ACCESS'])
// 		->alias('a')
// 		->join($this->_config['AUTH_GROUP'].' g','a.group_id = g.id')
// 		->where("a.uid='$uid' and g.status='1'")
// 		->column('uid,group_id,title,rules');
		$user_groups = Db::query("select uid,group_id,title,rules from {$this->_config['AUTH_GROUP_ACCESS']} a inner join {$this->_config['AUTH_GROUP']} g on a.group_id = g.id where a.uid={$uid} and g.status = 1");
		
		$groups[$uid] = $user_groups ?: array();
		return $groups[$uid];
	}
	
	/**
	 * 获得权限列表
	 * @param integer $uid  用户id
	 * @param integer $type
	 */
	protected function getAuthList($uid, $type)
	{
		static $_authList = array(); //保存用户验证通过的权限列表
		$t                = implode(',', (array) $type);
		if (isset($_authList[$uid . $t])) {
			return $_authList[$uid . $t];
		}
		if (2 == $this->_config['AUTH_TYPE'] && isset($_SESSION['_AUTH_LIST_' . $uid . $t])) {
			return $_SESSION['_AUTH_LIST_' . $uid . $t];
		}
	
		//读取用户所属用户组
		$groups = $this->getGroups($uid);
		$ids    = array(); //保存用户所属用户组设置的所有权限规则id
		foreach ($groups as $g) {
			$ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
		}
		$ids = array_unique($ids);
		if (empty($ids)) {
			$_authList[$uid . $t] = array();
			return array();
		}
	
		$map = [
				'id'     => array('in', $ids),
				'type'   => $type,
				//'status'=>1,
		];
		//读取用户组所有权限规则
		$rules = Db::table($this->_config['AUTH_RULE'])->where($map)->order('sort')->column('id,name,title,css,pid,levels,sort,status');
		$this->authMenu = $rules;
		//循环规则，判断结果。
		$authList = array(); //
		foreach ($rules as $rule) {
			if (!empty($rule['cond'])) {
				//根据cond进行验证
				$user = $this->getUserInfo($uid); //获取用户信息,一维数组
	
				$command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['cond']);
				//dump($command);//debug
				@(eval('$cond=(' . $command . ');'));
				if ($cond) {
					$authList[] = strtolower($rule['name']);
				}
			} else {
				//只要存在就记录
				$authList[] = strtolower($rule['name']);
			}
		}
		$_authList[$uid . $t] = $authList;
		if (2 == $this->_config['AUTH_TYPE']) {
			//规则列表结果保存到session
			$_SESSION['_AUTH_LIST_' . $uid . $t] = $authList;
		}
		return array_unique($authList);
	}
	
	/**
	 * 获得用户资料,根据自己的情况读取数据库
	 */
	protected function getUserInfo($uid)
	{
		static $userinfo = array();
		if (!isset($userinfo[$uid])) {
			$userinfo[$uid] = Db::table($this->_config['AUTH_USER'])->where('uid',$uid)->find();
		}
		return $userinfo[$uid];
	}

}
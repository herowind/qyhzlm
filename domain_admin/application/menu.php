<?php
return	[
	'spread' => ['name' => '推广','child' => [
					['name' => '朋友圈','child' => [
						['name'=>'申请列表','act'=>'publishList','op'=>'Weixinad' ,'mod'=>'spread'],
						['name'=>'发布列表','act'=>'publishList','op'=>'Weixinad' ,'mod'=>'spread'],
					]],
				]],	
	'system' => ['name' => '系统','child' => [
					['name' => '设置','child' => [
						['name'=>'微信设置','act'=>'publishList','op'=>'Weixinad','mod'=>'spread'],
						['name'=>'短信模板','act'=>'publishList','op'=>'Weixinad','mod'=>'spread'],
					]],
					['name' => '会员','child' => [
						['name'=>'会员列表','act'=>'index','op'=>'System' ,'mod'=>'system'],
						['name'=>'充值记录','act'=>'region','op'=>'Tools' ,'mod'=>'system'],
						['name'=>'提现申请','act'=>'navigationList','op'=>'System' ,'mod'=>'system']
					]],	
					['name' => '权限','child' => [
						['name'=>'管理员列表','act'=>'index','op'=>'System' ,'mod'=>'system'],
						['name'=>'角色','act'=>'region','op'=>'Tools' ,'mod'=>'system'],
						['name'=>'权限资源列表','act'=>'navigationList','op'=>'System' ,'mod'=>'system'],
						['name'=>'管理员日志','act'=>'navigationList','op'=>'System' ,'mod'=>'system']
					]],	
					['name' => '数据','child' => [
						['name'=>'数据备份','act'=>'index','op'=>'System' ,'mod'=>'system'],
						['name'=>'数据还原','act'=>'region','op'=>'Tools' ,'mod'=>'system'],
					]],
				]],

];
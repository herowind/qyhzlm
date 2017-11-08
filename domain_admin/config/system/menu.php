<?php
return	[
	'alliance'=> ['name' => '联盟商家','child' => [
					['name' => '基础设置','child' => [
					    ['name'=>'分类查询' , 'mod'=>'alliance' ,'op'=>'cate' ,'act'=>'search'],
						['name'=>'表单查询' , 'mod'=>'alliance' ,'op'=>'form' ,'act'=>'search'],
					    ['name'=>'属性查询' , 'mod'=>'alliance' ,'op'=>'attribute' ,'act'=>'search'],
					]],
				]],	
    'screen' => ['name' => '霸屏','child' => [
                    ['name' => '屏幕','child' => [
                        ['name'=>'消息管理','act'=>'msgList','op'=>'Games' ,'mod'=>'screen'],
                        ['name'=>'屏幕设置','act'=>'index','op'=>'Setting' ,'mod'=>'screen'],
                        ['name'=>'屏幕通知','act'=>'notice','op'=>'Setting' ,'mod'=>'screen'],
                        ['name'=>'上墙信息','act'=>'baseinfo','op'=>'Setting' ,'mod'=>'screen'],
                        ['name'=>'屏幕管理员','act'=>'adminList','op'=>'Member' ,'mod'=>'screen'],
                    ]],
        
                    ['name' => '游戏','child' => [
                        ['name'=>'霸屏','act'=>'msgList','op'=>'Gamebaping' ,'mod'=>'screen'],
                        ['name'=>'打赏','act'=>'msgList','op'=>'Gamedashang' ,'mod'=>'screen'],
                    ]],
                    ['name' => '客户','child' => [
                        ['name'=>'客户管理','act'=>'memberList','op'=>'Member' ,'mod'=>'screen'],
                        ['name'=>'黑名单','act'=>'blackList','op'=>'Member' ,'mod'=>'screen'],
                    ]],
                    ['name' => '收入','child' => [
                        ['name'=>'收入明细','act'=>'orderList','op'=>'Finance' ,'mod'=>'screen'],
                        ['name'=>'提现明细','act'=>'withdrawList','op'=>'Finance' ,'mod'=>'screen'],
                    ]],
                    ['name' => '统计','child' => [
                        ['name'=>'统计概况','act'=>'index','op'=>'Outline' ,'mod'=>'screen'],
                    ]],
                ]],
    'pintu' =>  ['name' => '城市拼途','child' => [
                    ['name' => '预约包车','child' => [
                        ['name'=>'包车查询','mod'=>'pintu' ,'op'=>'appt' ,'act'=>'search'],
                        ['name'=>'发布按天包车','mod'=>'pintu' ,'op'=>'appt' ,'act'=>'add_day'],
                        ['name'=>'发布城际顺风车','mod'=>'pintu' ,'op'=>'appt' ,'act'=>'add_city'],
                        ['name'=>'价格速查','mod'=>'pintu' ,'op'=>'fee' ,'act'=>'search'],
                        ['name'=>'站点查询','mod'=>'pintu' ,'op'=>'station' ,'act'=>'search'],
                    ]],
        
                    ['name' => '车主管理','child' => [
                        ['name'=>'车主查询' , 'mod'=>'pintu' ,'op'=>'driver' ,'act'=>'search'],
                        ['name'=>'车辆查询' , 'mod'=>'pintu' ,'op'=>'driver_car' ,'act'=>'search'],
                    ]],
                
                    ['name' => '商家管理','child' => [
                        ['name'=>'商家查询','act'=>'search','op'=>'Shop' ,'mod'=>'pintu'],
                        ['name'=>'新增商家','act'=>'add','op'=>'Shop' ,'mod'=>'pintu'],
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
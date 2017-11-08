<?php
namespace app\core_api\service;
use think\Facade\Log;
use think\Exception;
use com\utils\FncRequest;
use com\utils\wxapp\WxDataCrypt;
use app\core_api\model\User;

class LoginService {
	public static function getAuthInfo($appid,$appsecret,$code){
		$url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$appsecret}&js_code={$code}&grant_type=authorization_code";
		$timeout = 15 * 1000;
		
		$begin = round(microtime(TRUE) * 1000);
		list($status, $body) = array_values(FncRequest::get(compact('url', 'timeout')));
		$end = round(microtime(TRUE) * 1000);
		
		// 记录请求日志
		Log::record("POST {$url}} => [{$status}],[响应],[耗时]:sprintf('%sms', $end - $begin)");
		if ($status !== 200) {
			throw new Exception('请求鉴权 API 失败，网络异常或鉴权服务器错误');
		}
		
		if (!is_array($body)) {
			throw new Exception('鉴权服务器响应格式错误，无法解析 JSON 字符串');
		}
		
		if (isset($body['errcode'])) {
			throw new Exception("鉴权服务调用失败：{$body['errcode']} - {$body['errmsg']}", $body['errcode']);
		}
		Log::record("{$body['openid']},{$body['session_key']},{$body['expires_in']}");
		//openid,session_key,expires_in
		return $body;
	}
	/**
	 * 获取小程序appid appsecret
	 * @param unknown $agent_uid
	 * @return mixed
	 */
	public static function getAppInfo($agent_uid=0){
	    $appInfo['appid'] = 'wxa00efd88330a1911';//wxa00efd88330a1911
	    $appInfo['appsecret'] = 'ba2a408a29f3426db6642bec813faadc';//ba2a408a29f3426db6642bec813faadc
		return $appInfo;
	}
		
	/**
	 * 微信登录
	 * @param unknown $header
	 * @param unknown $agent_uid
	 * @return string|unknown
	 */
	public static function loginwx($header){
		try {
			// 使用登录凭证 code 
			$code = $header[Constants::WX_HEADER_CODE];	
				
			// 获得小程序appid，appsecret
			$appInfo = self::getAppInfo();
			
			// 获得用户 session_key 和 openid
			$authInfo = self::getAuthInfo($appInfo['appid'], $appInfo['appsecret'], $code);
			Log::record("wx-appid:{$appInfo['appid']},$code:{$code}");
			//$authInfo = ['openid'=>'oGZUI0egBJY1zhBYw2KhdUfwVJJE','session_key'=>'tiihtNczf5v6AKRyjwEUhQ==','expires_in'=>2592000];
			
			$user = User::where('openid',$authInfo['openid'])->field('id,openid,unionid,realname,avatar,gender,birthday,city,province,mobile,issecret,address,addressla,addresslo')->find();
			if(empty($user)){
				$encryptData = $header[Constants::WX_HEADER_ENCRYPT_DATA];
				//$encryptData='CiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZMQmRzooG2xrDcvSnxIMXFufNstNGTyaGS9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+3hVbJSRgv+4lGOETKUQz6OYStslQ142dNCuabNPGBzlooOmB231qMM85d2/fV6ChevvXvQP8Hkue1poOFtnEtpyxVLW1zAo6/1Xx1COxFvrc2d7UL/lmHInNlxuacJXwu0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn/Hz7saL8xz+W//FRAUid1OksQaQx4CMs8LOddcQhULW4ucetDf96JcR3g0gfRK4PC7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns/8wR2SiRS7MNACwTyrGvt9ts8p12PKFdlqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYVoKlaRv85IfVunYzO0IKXsyl7JCUjCpoG20f0a04COwfneQAGGwd5oa+T8yO5hzuyDb/XcxxmK01EpqOyuxINew==';
				$iv = $header[Constants::WX_HEADER_IV];
				//$iv = 'r7BXXKkLb8qrSNn05n0qiA=='; 
				Log::record("wx-data:{$encryptData},iv:{$iv}");
				$crypt = new WxDataCrypt($appInfo['appid'], $authInfo['session_key']);
				$wxUser = null;
				$errCode = $crypt->decryptData($encryptData, $iv, $wxUser );
				Log::record("wx-userInfo:{$errCode}");
				if($errCode == 0){
					$wxUser = json_decode($wxUser,true);
					$user['openid'] 	= $wxUser['openId'];
					$user['avatar'] 	= $wxUser['avatarUrl'];
					$user['city'] 		= $wxUser['city'];
					$user['province'] 	= $wxUser['province'];
					$user['country'] 	= $wxUser['country'];
					$user['nickname'] 	= $wxUser['nickName'];
					$user['realname'] 	= $wxUser['nickName'];
					$user['gender'] 	= $wxUser['gender'];
					$user['appid'] 		= $wxUser['watermark']['appid'];
					$user['unionid'] 	= $wxUser['unionId'];
					$user = User::create($user);
					if($user === false){
						$rtnData[Constants::WX_SESSION_MAGIC_ID] = 1;
						$rtnData['error'] = Constants::ERR_LOGIN_FAILED;
						$rtnData['message'] = '用户登录信息写入失败！';
					}
				}
			}
			//写入session数据
			Log::record($user['id']);
			$rtnData['session']['user'] 			    = $user;
			$rtnData['session']['id'] 					= $authInfo['openid'];
			//$rtnData['session']['skey'] 				= $authInfo['session_key'];
			$rtnData['session']['skey'] 				= uniqid($user['id']);
			$rtnData[Constants::WX_SESSION_MAGIC_ID] = 1;
			cache($rtnData['session']['skey'],$rtnData['session']);
			
			//session处理完毕
			return $rtnData;
		
		} catch (Exception $e) {
			$rtnData[Constants::WX_SESSION_MAGIC_ID] = 1;
			$rtnData['error'] = Constants::ERR_LOGIN_FAILED;
			$rtnData['message'] = $e->getMessage();
			return $rtnData;
		}
	}

    public static function checkwx($header) {
		$id = $header[Constants::WX_HEADER_ID];
		$skey = $header[Constants::WX_HEADER_SKEY];
			
		$sData = cache($skey);
		if($sData && isset($sData['id']) &&isset($sData['user'])){
			return $sData['user'];
   		}else{
			return null;
		}
    }
    
    public static function updSession($header,$member){
    	$id = $header[Constants::WX_HEADER_ID];
    	$skey = $header[Constants::WX_HEADER_SKEY];
    	$sData = cache($skey);
    	if($sData && isset($sData['id']) &&isset($sData['userInfo'])){
    		$sData['session']['userInfo'] = $member;
    		cache($skey,$sData['session']);
    	}
    }
}

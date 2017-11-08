<?php
namespace com\utils\wxapp;

/**
 * Prpcrypt
 *
 * 提供基于PKCS7算法的加解密接口.
 */

class Prpcrypt{
	public static $OK = 0;
	public static $IllegalAesKey = -41001;
	public static $IllegalIv = -41002;
	public static $IllegalBuffer = -41003;
	public static $DecodeBase64Error = -41004;
	
	public $key;

	public function __construct( $k ){
		$this->key = $k;
	}

	/**
	 * 对密文进行解密
	 * @param string $aesCipher 需要解密的密文
     * @param string $aesIV 解密的初始向量
	 * @return string 解密得到的明文
	 */
	public function decrypt( $aesCipher, $aesIV )
	{

		try {
			
			$module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
			
			mcrypt_generic_init($module, $this->key, $aesIV);

			//解密
			$decrypted = mdecrypt_generic($module, $aesCipher);
			mcrypt_generic_deinit($module);
			mcrypt_module_close($module);
		} catch (\Exception $e) {
			return array(Prpcrypt::$IllegalBuffer, null);
		}


		try {
			//去除补位字符
			$pkc_encoder = new Pkcs7Encoder(); 
			$result = $pkc_encoder->decode($decrypted);

		} catch (\Exception $e) {
			//print $e;
			return array(Prpcrypt::$IllegalBuffer, null);
		}
		return array(0, $result);
	}
}

?>
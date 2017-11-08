<?php
namespace app\core_api\service;

class Constants {
	const WX_HEADER_HOST = 'host';
    const WX_HEADER_CODE = 'x-wx-code';
    const WX_HEADER_ENCRYPT_DATA = 'x-wx-encrypted-data';
    const WX_HEADER_IV = 'x-wx-iv';
    const WX_HEADER_ID = 'x-wx-id';
    const WX_HEADER_SKEY = 'x-wx-skey';

    const WX_SESSION_MAGIC_ID = 'F2C224D4-2BCE-4C64-AF9F-A6D872000D1A';

    const ERR_LOGIN_FAILED = 'ERR_LOGIN_FAILED';
    const ERR_INVALID_SESSION = 'ERR_INVALID_SESSION';
    const ERR_CHECK_LOGIN_FAILED = 'ERR_CHECK_LOGIN_FAILED';

    const INTERFACE_LOGIN = 'cityapp.id_skey';
    const INTERFACE_CHECK = 'cityapp.auth';

    const RETURN_CODE_SUCCESS = 0;
    const RETURN_CODE_SKEY_EXPIRED = 60011;
    const RETURN_CODE_WX_SESSION_FAILED = 60012;
}

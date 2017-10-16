<?php
namespace Dingtalk\Api;

use Dingtalk\Utils\Http;
use Dingtalk\Utils\Cache;

class Smlogin{

    private static $appConfig = [];

    public function __construct($config)
    {
        self::$appConfig = $config;
    }

    private function getAccessToken()
    {
        $accessToken = Cache::get('DING_smlogin_access_token');
        if (!$accessToken)
        {
            $appid = self::$appConfig['appid'];
            $appsecret = self::$appConfig['appsecret'];
            $response = Http::get('/sns/gettoken', array('appid' => $appid, 'appsecret' => $appsecret));
            $accessToken = $response->access_token;
            Cache::set('DING_smlogin_access_token', $accessToken);
        }
        return $accessToken;
    }

    /**
     * 获取永久授权码
     * @param $accessToken
     * @param $opt ["tmp_auth_code": "xxxxx"] //临时授权码
     * @return string
     */
    private function getPersistentCode($accessToken, $opt)
    {
        $response = Http::post('/sns/get_persistent_code',
            ['access_token'=>$accessToken],
            json_encode($opt));
        return $response;
    }

    /**
     * 获取用户授权的SNS_TOKEN
     * @param $accessToken
     * @param $opt
     * @return string
     */
    private function getSnsToken($accessToken, $opt)
    {
        $response = Http::post('/sns/get_sns_token',
            ['access_token'=>$accessToken],
            json_encode($opt));
        return $response->sns_token;
    }

    /**
     * 获取啊用户授权的个人信息
     * @param $snsToken
     * @return string
     */
    private function getUserInfo($snsToken)
    {
        $response = Http::get('/sns/getuserinfo', ['sns_token'=>$snsToken]);
        return $response->user_info;
    }

    public static function getUser($tmpCode)
    {
        $accessToken = self::getAccessToken();
        $persistentCode = self::getPersistentCode($accessToken,['tmp_auth_code'=>$tmpCode]);

        $snsToken = self::getSnsToken($accessToken,
            [
                "openid"=>$persistentCode->openid,
                "persistent_code"=>$persistentCode->persistent_code
            ]);
        $user = self::getUserInfo($snsToken);
        return $user;
    }
}
<?php
namespace app\dingtalk\controller;

use Dingtalk\Utils\Http;
use Dingtalk\Utils\Cache;
use think\Controller;

class Smlogin extends Controller {

    private static $appConfig = [];

    public function __construct()
    {
        self::$appConfig =[
            'appid'=>'dingoa70ok7bqooyhq9ttl',
            'agentId'=>'122700771',
            'corpId'=>'ding38872dd0a69908aa35c2f4657eb6378f',
            'corpSecret'=>'BBewgd8BudgGQJPjpbYRfN9jmc9aDKU6lxucRjK2NEr5c8ei3HUV_NQaDpa_-Qzv',
            'ssoSecret'=>'sTNu4D_bAW30LLtPqPQtm0Xeo4T0WW6J84XbdQtwu3QAMvz9RT-U0Ey0zF0dPRtS',
            'apiHost'=>'https://oapi.dingtalk.com'
        ];
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

    public static function getUser($code)
    {
        $accessToken = self::getAccessToken();
        $persistentCode = self::getPersistentCode($accessToken,['tmp_auth_code'=>$code]);

        $snsToken = self::getSnsToken($accessToken,
            [
                "openid"=>$persistentCode->openid,
                "persistent_code"=>$persistentCode->persistent_code
            ]);
        $user = self::getUserInfo($snsToken);
        return $user;
    }
}
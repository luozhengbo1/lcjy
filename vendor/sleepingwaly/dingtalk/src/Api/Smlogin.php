<?php
namespace Dingtalk\Api;

use app\common\controller\DingtalkBase;
use Dingtalk\Utils\Cache;
use Dingtalk\Utils\Http;
use think\Config;
use think\Request;
use think\Session;

class Smlogin extends  DingtalkBase {

    protected $http;
    public function _initialize()
    {
      parent::_initialize();

    }


    public function getAccessToken()
    {
        $accessToken = Session::get('DING_smlogin_access_token');
        if (!$accessToken)
        {
//            $appid =Config::get('ddconfig.appid');
            $appid ='dingoapzw2ktnvznxzvxcb';
//            $appsecret = Config::get('appsecret');
            $appsecret ='OwXyIUQOHDh2vkpkHQqtAybwEks2tgyhE-P524ltKtyR-GIPK5AO7jumNIaRlIMC';
            $response =Http::get('/sns/gettoken', array('appid' => $appid, 'appsecret' => $appsecret));
            $accessToken = $response->access_token;
            Session::set('DING_smlogin_access_token', $accessToken);
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
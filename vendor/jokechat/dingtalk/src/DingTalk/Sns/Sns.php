<?php
namespace DingTalk\Sns;
use DingTalk\Util\Config;
use DingTalk\Token\AccessToken;
use DingTalk\Util\Curl;
class Sns
{
    //伪需求 设置curl请求头
    private static $headers  = array("Content-type: application/json;charset='utf-8'","Accept: application/json");
    
    /**
     * 根据是授权code换取临时授权token令牌
     * @param string $tmp_auth_code  临时授权码
     * @return boolean|mixed
     * 成功返回 {"errcode":0,"errmsg":"ok","openid":"Of4Za3liiqj4iE","persistent_code":"slpjvCktaJukJ0d74irj2GAC4cX7DtuUcWTl2JVSvtS1BYL0MpplANE4pVSKEeDq","unionid":"4u5iirmEXI9oiE"}
     * 失败 false
     */
    public  function getSnsPersistentCode($tmp_auth_code)
    {
        $ddconfig                      = Config::getConfig();
        $flag 							= false;
        $access_token 				= AccessToken::getSnsAccessToken();

        $queryUrl 						= $ddconfig['get_persistent_code'];
        $param["tmp_auth_code"] 	= $tmp_auth_code;
        $param['access_token']      = $access_token;
//        return $access_token;
        $headers['10023']  				= self::$headers;
        curl::setOption($headers);
//        $queryUrl = $queryUrl.http_build_query($param);
        $queryUrl = $queryUrl.'?access_token='.$access_token;
        $auth_info 						= Curl::callWebServer($queryUrl,json_encode($param),'POST',true);
        if ($auth_info['errcode'] ==  0)
        {
            $flag 	= $auth_info ;
        }
        return $auth_info;
    }
    
    /**
     * 获取用户授权的SNS_TOKEN 有效期仅有两个小时
     * @param string $openid
     * @param string $persistent_code
     * @return array
     * array(4) {
     ["errcode"] => int(0)
     ["errmsg"] => string(2) "ok"
     ["expires_in"] => int(7200)
     ["sns_token"] => string(32) "d206fdd7b9ad3bcb9918800a324be339"
     }
     */
    public  function getSnsToken($openid,$persistent_code)
    {
        $access_token 				= AccessToken::getSnsAccessToken();
        $config                         = Config::getConfig();
//        $ddconfig                      = $config['params']['dd'];
        $ddconfig                      = $config;
        $queryUrl 						= "https://oapi.dingtalk.com/sns/get_sns_token/?access_token=$access_token";
        $param["openid"] 			= $openid;
        $param["persistent_code"] = $persistent_code;
    
        $headers['10023']  			= self::$headers;
        curl::setOption($headers);
    
        $snsToken 					= Curl::callWebServer($queryUrl,json_encode($param),'POST');
        return $snsToken;
    }
    
    /**
     * 通过sns_token  获取授权用户信息
     * @param array $sns_token
     * @return array
     * {"errcode":0,"errmsg":"ok","user_info":{"dingId":"$:LWCP_v1:$BSEbqoWIL3wJ9AXTjVsAJw==","nick":"张张","openid":"Of4Za3liiqj4iE","unionid":"4u5iirmEXI9oiE"}}
     */
    public static function getSnsUserInfo($sns_token)
    {
        $ddconfig                      = \think\Config::get('ddconfig');
//        return $ddconfig['sns_getuserinfo'];
//        $queryUrl 						= $ddconfig['sns_getuserinfo']."sns_token=$sns_token";
        $queryUrl 						= 'https://oapi.dingtalk.com/sns/getuserinfo/?sns_token='.$sns_token;
        $sns_userinfo 				= Curl::callWebServer($queryUrl);
        return $sns_userinfo;
    }
}
?>
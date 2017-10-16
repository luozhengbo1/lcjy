<?php
namespace DingTalk\Token;
use DingTalk\Util\Config;
use think\Session;
use Unirest\Request;

/**
 * d钉钉token令牌的获取与过期检查
 * @author jokechat
 * @date 2017年2月9日
 */
class AccessToken
{
    /**
     * 获取钉钉access_token
     * @return mixed|boolean
     */
    public static function getAccessToken()
    {
        $accessToken    = self::_checkAccessToken();
        if ($accessToken === false ) {
            $accessToken = self::_getAccessToken();
        }
        return $accessToken;
    }

    /**
     * 缓存中token
     * @return mixed|boolean
     */
    private static function _checkAccessToken()
    {
        // 此处通常从database / redis /memcached 获取
        $data = \session('access_token');
//        $access_token = json_decode($data,true);
        $access_token = $data;
        if(!empty($access_token->access_token))
        {
            if((time() - $access_token->time) < 3500 )
            {
                return $access_token->access_token;
            }
        }
        return false;
    }

    /**
     * 从钉钉服务器获取token令牌
     * @return boolean
     */
    private static function _getAccessToken()
    {
        $ddconfig                  = Config::getConfig();
        $queryUrl 				    = $ddconfig['gettoken_url'];
        $param["corpid"] 	    = $ddconfig['corpid'];
        $param["corpsecret"] 	= $ddconfig['corpsecret'];
        $headers                   = array('Accept' => 'application/json');
        $response                  = Request::get($queryUrl,$headers,$param);
        $result                       = $response->body;
        $result->time             = time();
        if (property_exists($result, "access_token"))
        {
            $accessToken   = $result->access_token;
            // 此处通常会存入database / redis /memcached
//	        $f = fopen('access_token', 'w+');
//	        fwrite($f, json_encode($result));
//	        fclose($f);

            Session::set('access_token',json_encode($result));
        }else
        {
            $accessToken   = false;
        }
        return $accessToken;
    }

    /**
     * 获取sns  access_token
     * @return string sns accessToken
     */
    public static function getSnsAccessToken()
    {
        $SnsAccessToken    = self::_checkSnsAccessToken();
        if ($SnsAccessToken === false ) {
            $SnsAccessToken = self::_getSnsAccessToken();
        }
        return $SnsAccessToken;
    }

    /*
     * 缓存中的Sns_token
     */
    private static function _checkSnsAccessToken()
    {
        // 此处通常从database / redis /memcached 获取
        $data = \session('sns_access_token');
//        $access_token = json_decode($data,true);
        $sns_access_token = $data;
        if(!empty($sns_access_token->access_token))
        {
            if((time() - $sns_access_token->time) < 3500 )
            {
                return $sns_access_token->access_token;
            }
        }
        return false;
    }

    /**
     * 从钉钉服务器获取token令牌
     * @return boolean
     */
    private static function _getSnsAccessToken()
    {
        $config                 = Config::getConfig();
        $queryUrl 				= $config['get_sns_token_url'];
        $param["appid"] 		= $config['appid'];
        $param["appsecret"] 	= $config['appsecret'];
        $param 					= http_build_query ($param);
        $headers                = array('Accept' => 'application/json');
        $queryUrl               = $queryUrl."?".$param;
        $response               = Request::get($queryUrl,$headers);
        $result                 = $response->body;
        if (property_exists($result, "access_token"))
        {
            // 此处通常会存入database / redis /memcached
            $accessToken   = $result->access_token;
            Session::set('sns_access_token',json_encode($result));
        }else
        {
            $accessToken   = false;
        }
        return $accessToken;
    }
}
?>

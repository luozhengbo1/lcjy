<?php

namespace app\dingtalk\controller;
use Dingtalk\Utils\Http;
use Dingtalk\Utils\Cache;
use think\Session;
class Auth
{
    public static function getAccessToken()
    {
        $response = Http::get('/gettoken', array('corpid' => 'ding38872dd0a69908aa35c2f4657eb6378f', 'corpsecret' => 'BBewgd8BudgGQJPjpbYRfN9jmc9aDKU6lxucRjK2NEr5c8ei3HUV_NQaDpa_-Qzv'));
        return $response->access_token;
    }
    
    public static function getTicket()
    {
        $response = Http::get('/get_jsapi_ticket', array('type' => 'jsapi', 'access_token' => self::getAccessToken() ) );
        return $response->ticket;
    }
    
    
    public static function getConfig()
    {
        $nonceStr = 'abcdefg';
        $timeStamp = time();
        $url = self::getCurrentUrl();
        // $url = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
        
        $accessToken = self::getAccessToken();
        $ticket = self::getTicket($accessToken);
        $signature = self::sign($ticket, $nonceStr, $timeStamp, $url);
        
        $config = array(
            'url' => $url,
            'nonceStr' => $nonceStr,
            'timeStamp' => $timeStamp,
            'corpId' => 'ding38872dd0a69908aa35c2f4657eb6378f',
            'signature' => $signature);
        return json_encode($config, JSON_UNESCAPED_SLASHES);
    }
    
    
    public static function sign($ticket, $nonceStr, $timeStamp, $url)
    {
        $plain = 'jsapi_ticket=' . $ticket .
            '&noncestr=' . $nonceStr .
            '&timestamp=' . $timeStamp .
            '&url=' . $url;
        return sha1($plain);
    }
    
    
    private static function getCurrentUrl() 
    {
        $url = "http";
//      if ($_SERVER["HTTPS"] == "on") 
//      {
//          $url .= "s";
//      }
        $url .= "://";
    
        if ($_SERVER["SERVER_PORT"] != "80") 
        {
            $url .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } 
        else 
        {
            $url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $url;
    }
}
<?php
namespace Dingtalk;

use Dingtalk\Utils\Http;
use Dingtalk\Utils\Cache;
use Dingtalk\Utils\Log;
use think\Config;

class Dingtalk
{
    protected static $instance = null;

    protected static $config = [
        'appid'=>'dingoa70ok7bqooyhq9ttl',
        'agentId'=>'122700771',
        'corpId'=>'ding38872dd0a69908aa35c2f4657eb6378f',
        'corpSecret'=>'BBewgd8BudgGQJPjpbYRfN9jmc9aDKU6lxucRjK2NEr5c8ei3HUV_NQaDpa_-Qzv',
        'ssoSecret'=>'sTNu4D_bAW30LLtPqPQtm0Xeo4T0WW6J84XbdQtwu3QAMvz9RT-U0Ey0zF0dPRtS',
        'apiHost'=>'https://oapi.dingtalk.com'
    ];

    protected static $error = null;

    private function __construct()
    {
    }

    public static function getInstance($config = [])
    {
        if(is_null(self::$instance)){
            self::$config = array_merge(self::$config,$config);
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function getAccessToken()
    {
        $accessToken = Config::get('corp_access_token');
        if (!$accessToken)
        {
            $response = Http::get(self::$config['apiHost'].'/gettoken', array('corpid' => self::$config['corpId'], 'corpsecret' => self::$config['corpSecret']));
            $accessToken = $response->access_token;
            Config::set('corp_access_token', $accessToken);
        }
        return $accessToken;
    }

    /**
     * 缓存jsTicket。jsTicket有效期为两小时，需要在失效前请求新的jsTicket（注意：以下代码没有在失效前刷新缓存的jsTicket）。
     */
    public static function getTicket($accessToken)
    {
        $jsticket = Config::get('js_ticket');
        if (!$jsticket)
        {
            $response = Http::get('/get_jsapi_ticket', array('type' => 'jsapi', 'access_token' => $accessToken));
            self::check($response);
            $jsticket = $response->ticket;
            Config::set('js_ticket',$jsticket);
        }
        return $jsticket;
    }
    public static function curPageURL()
    {
        $pageURL = 'http';
        if (array_key_exists('HTTPS',$_SERVER)&&$_SERVER["HTTPS"] == "on")
        {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80")
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        }
        else
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
    public static function getConfig()
    {
        $corpId = self::$config['corpId'];
        $agentId = self::$config['agentId'];
        $nonceStr = uniqid();
        $timeStamp = time();
        $url = self::curPageURL();
        $corpAccessToken = self::getAccessToken();
        if (!$corpAccessToken)
        {
            Log::e("[getConfig] ERR: no corp access token");
        }
        $ticket = self::getTicket($corpAccessToken);
        $signature = self::sign($ticket, $nonceStr, $timeStamp, $url);

        $config = array(
            'url' => $url,
            'nonceStr' => $nonceStr,
            'agentId' => $agentId,
            'timeStamp' => $timeStamp,
            'corpId' => $corpId,
            'signature' => $signature);
//        return json($config);
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

    public static function check($res)
    {
        if ($res->errcode != 0)
        {
            Log::e("FAIL: " . json_encode($res));
            exit("Failed: " . json_encode($res));
        }
    }

}

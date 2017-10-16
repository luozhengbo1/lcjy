<?php
namespace Dingtalk\Utils;

use Httpful\Request;

class Http extends Request{

    public static function get($uri, $mime = null)
    {
        $url = self::joinParams($uri, $mime);
        $response = Request::get($url)->send();
        return $response->body;
    }

    public static function post($uri, $payload = NULL, $mime = NULL)
    {
        $url = self::joinParams($uri, $payload);
        $response = Request::post($url)
            ->body($mime)
            ->sendsJson()
            ->send();
        return $response->body;
    }

    private static function joinParams($path, $params)
    {
        $path = preg_match('/^http/',$path) ? $path : 'https://oapi.dingtalk.com'.$path;
        return $path . '?' . http_build_query($params);
    }

}


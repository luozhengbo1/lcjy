<?php
namespace Dingtalk\Api;

use Dingtalk\Utils\Http;

class Microapp
{
    public static function visibleScopes($accessToken, $agendId)
    {
        $response = Http::post("/microapp/visible_scopes",
            array("access_token" => $accessToken),
            json_encode(array("agentId"=>$agendId)));
        return $response;
    }
}
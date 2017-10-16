<?php
namespace Dingtalk\Api;

use Dingtalk\Utils\Http;

class User
{
    public static function getUserInfo($accessToken, $code)
    {
        $response = Http::get("/user/getuserinfo", 
            array("access_token" => $accessToken, "code" => $code));
        return $response;
    }

    public static function simplelist($accessToken,$deptId){
        $response = Http::get("/user/simplelist",
            array("access_token" => $accessToken,"department_id"=>$deptId));
        return $response->userlist;

    }

    /**
     * 获取部门成员(详情列表)
     * @param $accessToken
     * @param $deptId 部门id
     * @return mixed
     */
    public static function userList($accessToken,$deptId){
        $response = Http::get("/user/list",
            ['access_token'=>$accessToken,'department_id'=>$deptId]);
        return $response->userlist;
    }

    public function userDetails($accessToken, $userId){
        $response = Http::get("/user/get",
            ['access_token'=>$accessToken,'userid'=>$userId]);
        return $response;
    }

    /**
     * 根据unionid获取成员userid
     * @param $accessToken
     * @param $unionid
     * @return string
     */
    public static function getUseridByUnionid($accessToken,$unionid){
        $response = Http::get("/user/getUseridByUnionid",
            ['access_token'=>$accessToken,'unionid'=>$unionid]);
        return $response;
    }
}
<?php
namespace app\common\controller;

use think\Cache;
use think\Controller;
use think\Db;
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET');
header('Access-Control-Allow-Credentials:true');
header("Content-Type: application/json;charset=utf-8");

class ApiBase extends Controller
{

    protected function _initialize()
    {
        parent::_initialize();
    }

}
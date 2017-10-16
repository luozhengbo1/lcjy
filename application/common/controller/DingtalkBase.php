<?php
namespace app\common\controller;

use DingTalk\Token\AccessToken;
use think\Cache;
use think\Controller;
use think\Db;


class DingtalkBase extends Controller
{

    protected function _initialize()
    {
        parent::_initialize();
    }
}
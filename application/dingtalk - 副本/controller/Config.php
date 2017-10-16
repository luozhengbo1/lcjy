<?php
/**
 * Created by PhpStorm.
 * User: ran
 * Date: 2017/9/7
 * Time: 上午10:11
 */

namespace app\dingtalk\controller;


use Dingtalk\Dingtalk;
use think\Controller;

class Config extends Controller
{

    public static function getconfig()
    {
        $dingtalk = Dingtalk::getConfig();
        return $dingtalk;
    }
}
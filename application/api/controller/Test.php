<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/2
 * Time: 10:58
 */

namespace app\api\controller;

use think\cache;
use think\Controller;

class Test extends Controller
{

    public function index()
    {
        return check_dealer_id(2);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: ran
 * Date: 2017/8/31
 * Time: 上午10:41
 */

namespace app\tools\controller;


use think\Controller;

class Test extends Controller
{

    public function index()
    {
        return get_fenyong_amount(get_fenyong_percent(check_dealer_id(2)),200);
    }
}
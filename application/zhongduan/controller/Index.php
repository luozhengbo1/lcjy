<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/2
 * Time: 15:03
 */

namespace app\zhongduan\controller;


use think\Controller;

class Index extends Controller
{

    public function index()
    {
        return $this->fetch();
    }
}
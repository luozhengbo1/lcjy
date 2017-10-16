<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/3
 * Time: 10:07
 */

namespace app\wechat\controller;


use EasyWeChat\Foundation\Application;
use think\Config;
use think\Controller;
use think\Session;

class Wechat1 extends Controller
{

    public function index(){
        $options = Config::get('wechat');
        $app = new Application($options);
        $oauth =$app->oauth;
        $user = $oauth->user();
        $userinfo = $user->toArray();
        session('weixin_user',$userinfo);
        session('weixin_user_openid',$user->getId());
        $targetUrl = session('target_url');
        $this->redirect($targetUrl);
    }
}
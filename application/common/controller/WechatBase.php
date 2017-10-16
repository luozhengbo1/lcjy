<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/2
 * Time: 17:04
 */

namespace app\common\controller;


use app\wechat\controller\Check;
use EasyWeChat\Foundation\Application;
use think\Config;
use think\Controller;
use think\Session;

header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:POST,GET');
header('Access-Control-Allow-Credentials:true');
header("Content-Type: application/json;charset=utf-8");
class WechatBase extends Controller
{

    protected $option;
    protected $app;
    protected $get_access_token;
    protected $openid;
    protected function _initialize()
    {
        parent::_initialize();
        $this->option = Config::get('wechat');
        $this->app = new Application($this->option);
        $this->get_access_token = get_wechat_token();

        $this->openid = Session::get('weixin_user_openid');

        //判断openid是否为空，为空则获取当前方法的参数和方法名，存储缓存中
        //要求重新授权，同时判断是否已经注册，注册了则跳转之原来的方法页面，否则跳转到注册页面
        if(empty($this->openid)){
            $oauth = $this->app->oauth;
            //获取当前方法的参数，组合当前方法，然后存储到session中
            $param = http_build_query(request()->param());
            session('target_url',$_SERVER['PATH_INFO'].'?'.$param);

            $user = Session::get('weixin_user');
            if (empty($user)){
                $oauth->redirect('http://t.j9zz.com/wechat/wechat1')->send();
            }else{
                if(check_openid($this->openid)){
                    return $this->redirect('/wechat/register');
                }
            }
        }
    }

}
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

        if(empty($this->openid)){
            $oauth = $this->app->oauth;
//            Session::delete('target_url');
//            session('target_url',$_SERVER['PATH_INFO']);

            $param = http_build_query(request()->param());
            session('target_url',$_SERVER['PATH_INFO'].'?'.$param);

            $user = Session::get('weixin_user');
            if (empty($userssss)){
                $oauth->redirect('http://t.j9zz.com/wechat/wechat1')->send();
            }else{
                $user = session('wechat_user');
                if(check_openid($this->openid)){
                    return $this->redirect('/wechat/register');
                }
            }
        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: ran
 * Date: 2017/8/16
 * Time: 下午3:34
 */

namespace app\wechat\controller;


use EasyWeChat\Foundation\Application;
use think\Config;
use think\Controller;

class Check extends Controller
{

    protected $option;
    protected $app;
    protected $get_access_token;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\User();
        $this->config = Config::get('wechat');
        $this->app = new Application($this->config);
    }

    public function index()
    {
        $oauth = $this->app->oauth;
        // 未登录
        if (empty($_SESSION['wechat_user'])) {
            $_SESSION['target_url'] = '/wechat/check/profile';
            return $oauth->redirect();
            // 这里不一定是return，如果你的框架action不是返回内容的话你就得使用
            // $oauth->redirect()->send();
        }
        // 已经登录过
        $user = $_SESSION['wechat_user'];

        Session::set('weixin_user_openid',$user->getId());
        Session::set('wechat_user',$user);
    }

    public function profile()
    {
        $oauth = $this->app->oauth;

        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        Session::set('weixin_user',$user);
        Session::set('weixin_user_token',$user->getToken());
        Session::set('weixin_user_openid',$user->getId());
        if(check_openid($user['id']) ==false){
            $this->assign('userinfo',$user);
//            return $this->fetch('user/register');
            return json(1);
        } else{
            $this->assign('user/userinfo',$user);
            return $this->fetch();
        }
    }
}
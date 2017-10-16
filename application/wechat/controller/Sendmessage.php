<?php
/**
 * Created by PhpStorm.
 * User: ran
 * Date: 2017/9/13
 * Time: 下午5:09
 */

namespace app\wechat\controller;


use app\common\controller\WechatBase;
use EasyWeChat\Message\Text;

class Sendmessage extends WechatBase
{

    protected function _initialize()
    {
        return parent::_initialize();
    }

    /** 主动消息发送接口
     * @param $openid
     */
    public function index($message)
    {
        $message = new Text(['content' => $message]);
        $result = $this->app->staff->message($message)->to($this->openid)->send();

    }
}
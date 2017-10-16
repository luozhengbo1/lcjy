<?php
/**
 * Created by PhpStorm.
 * User: ran
 * Date: 2017/8/24
 * Time: 下午4:25
 */

namespace app\dingtalk\controller;


use Dingtalk\Dingtalk;
use DingTalk\Sns\Sns;
use think\Controller;

class Login extends Controller
{

    public function callback()
    {
        if(request()->isPost()){
           return json(Dingtalk::getConfig());
        } else {
            $code = request()->param('code');
            $sns = new Sns();
            $result = $sns->getSnsPersistentCode($code);
            $this->assign('config',json_decode(Dingtalk::getConfig(),true));
            return $this->fetch();
        }
    }
}
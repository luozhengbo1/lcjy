<?php
/**
 * Created by PhpStorm.
 * User: ran
 * Date: 2017/9/1
 * Time: 下午3:36
 */

namespace app\api\controller;


use app\common\model\Dealer;
use app\common\model\User;
use think\Controller;

class Mobile extends Controller
{

    public function checkForUserRegister($mobile)
    {
        $model = new User();
        $result = $model->where('mobile',$mobile)->value('id');
        if($result){
            return json(['code'=>1,'msg'=>'错误，该号码已经被注册']);
        } else{
            return json(['code'=>0,'msg'=>'该号码可以注册']);
        }
    }

    /**
     * 商家注册手机验证
     * @param $mobile
     * @return \think\response\Json
     */
    public function checkForSjRegister($mobile)
    {
        $model = new Dealer();
        $result = $model->where('tel',$mobile)->value('id');
        if(empty($result)){
            return json(['code'=>1,'msg'=>'抱歉，商户中心仅向商家用户开放']);
        } else{
            return json(['code'=>0,'msg'=>'该号码可以注册']);
        }
    }
}
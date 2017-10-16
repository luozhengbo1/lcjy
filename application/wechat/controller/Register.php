<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/22
 * Time: 00:06
 */

namespace app\wechat\controller;


use app\common\model\Dealer;
use app\common\model\SmsLog;
use EasyWeChat\Foundation\Application;
use think\Config;
use think\Controller;
use think\Session;

class Register extends Controller
{

    protected $model;
    protected function _initialize()
    {
        parent::_initialize();
        $this->model =new  \app\common\model\User();
        $this->option = Config::get('wechat');
        $this->app = new Application($this->option);
        $this->get_access_token = get_wechat_token();
        $this->openid = Session::get('weixin_user_openid');
        
    }

    public function index()
    {
        $smsLog = new SmsLog();
        $smsLog->where(['create_time'=>['<',time()-300]])->delete();
        if (request()->isPost()) {
            $data = request()->param();
            $from = Session::get('from');
//            $data['from'] = substr($from, 0, strpos($from, '.'));
//            $data['from'] = $data['src'];
//            $data['from'] =$from;
            $sms_id = $data['sms_id'];
            $code = $data['code'];
            $checksms = $smsLog->where(['sms_id'=>$sms_id,'code'=>$code])->find();
            if(empty($checksms)){
                return json(['code'=>2017,'msg'=>'验证码不正确']);
            }
            if ($this->model->allowField(true)->save( $data )) {
                $smsLog->where(['sms_id'=>$sms_id,'code'=>$code])->delete();
                return json(['code' => 2000, 'msg' => '注册成功']);
            } else {
                return json(['code' => 2016, 'msg' => '注册失败']);
            }
        } else{
            $this->assign('userinfo',Session::get('weixin_user'));
//            return json(Session::get('weixin_user'));
            //判断是否已经注册
            if(check_openid($this->openid)){
                return redirect('/wechat/user/userinfo');
            } else{
                $target_url = \session('target_url');
                //判断是否为从其他页面跳转到注册页面的
                if(!is_null($target_url)){
                    $this->assign('target_url',$target_url);
                }else{
                    $this->assign('target_url','/wechat/user/userinfo');
                }
                return $this->fetch('user/register');
            }
        }



    }

    /*
     * 商家注册
     */
    public function shangjiaRegister()
    {
        $smsLog = new SmsLog();
        $smsLog->where(['create_time'=>['<',time()-300]])->delete();

        if (request()->isPost()) {
            $data = request()->param();
            $from = Session::get('from');
//            $data['from'] = substr($from, 0, strpos($from, '.'));
            $sms_id = $data['sms_id'];
            $code = $data['code'];
            //验证验验证码
            $checksms = $smsLog->where(['sms_id'=>$sms_id,'code'=>$code])->find();
            if(empty($checksms)){
                return json(['code'=>2017,'msg'=>'验证码不正确']);
            }
            // 检查用户（电话号码）是否和后台绑定的商家联系电话一致。切记：【当你看J8不懂的时候，Command(Ctrl)+b是最好的选择。】
            if(check_mobile_for_sj_register($data['tel']) ==false){
                return json(['code'=>2018,'msg'=>'抱歉，暂不开放给非合作商家']);
            }
            //提交注册信息
            $model = new Dealer();
            if ($model->allowField(true)->where('tel',$data['tel'])->setField('openid',$this->openid)) {
                //注册成功后删除验证码
                $smsLog->where(['sms_id'=>$sms_id,'code'=>$code])->delete();
                return json(['code' => 2000, 'msg' => '注册成功']);
            } else {
                return json(['code' => 2016, 'msg' => '注册失败']);
            }
        } else{
            if(check_openid_shangjia($this->openid) ==false){
                $this->assign('userinfo',\session('weixin_user'));
                $target_url = \session('target_url');
                //判断是否为从其他页面跳转到注册页面的
                if(!is_null($target_url)){
                    $this->assign('target_url',$target_url);
                }else{
                    $this->assign('target_url','/wechat/user/userinfo');
                }
                return $this->fetch('shangjia/register');
            } else{
                return redirect('/wechat/shangjia/profile');
            }
        }

    }
}
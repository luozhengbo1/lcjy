<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/4
 * Time: 09:33
 */

namespace app\wechat\controller;


use app\common\controller\WechatBase;
use app\common\model\UserAddress;
use EasyWeChat\Foundation\Application;
use think\Config;
use think\Log;
use think\Session;

class Order extends WechatBase
{
    protected $model;
    protected $options;
    protected $app;
    protected $config;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Order();
        $this->option = Config::get('wechat');
        $this->config = [
            'debug' => true,
            'app_id' => 'wx774bccff0312978f',
//            'appId' => 'wx774bccff0312978f',
            'secret' => '7e44b712a9735a6de6c8446825a2f49a',
            'token' => 'lcjj2017',
            'log' => [
                'level' => 'debug',
                'file' => 'runtime/log/easywechat.log',
            ],
            'oauth' => [
                'scopes' => ['snsapi_userinfo'],
                'callback' => '/api/wechat/callback',
            ],
            'payment' => [
                'merchant_id' => '1485409702',
                'key' => 'Gzlcjyjt20170725Liaoliutaocaozuo',
                'cert_path' => '/extend/cert/apiclient_cert.pem', // XXX: 绝对路径！！！！
                'key_path' => '/extend/cert/apiclient_key.pem',      // XXX: 绝对路径！！！！
                'notify_url' => 'http://t.j9zz.com/wechat/callback/index',       // 你也可以在下单时单独设置来想覆盖它

            ]];
        $this->app = new Application($this->config);
    }
    public function info($id = 0)
    {

        $info = $this->model->where('id', $id)->find();
        if (!empty($info)) {
            $info['unit'] = get_order_unit($info['type']);
            $info['type'] = get_order_type($info['type']);
            $info['image'] = 'http://t.j9zz.com' . $info['image'];
            $result = ['code' => '2000', 'msg' => '读取成功', 'data' => $info];
        } else {
            $result = ['code' => '2000', 'msg' => '读取失败', 'data' => $info];
        }
        $this->assign('info', $info);

        $userinfo = Session::get('weixin_user');
        $this->assign('userinfo', $userinfo);
        Log::record($userinfo);
        $payment = $this->app->payment;

        $attributes = [

            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => $info['title'],
            'detail'           => $info['title'],
            'out_trade_no'     => $info['trade_no'],
            'total_fee'        => $info['amount']*100, // 单位：分
            //'notify_url'       => '', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid' => $this->openid, // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，

            // ...
        ];

        $order = new \EasyWeChat\Payment\Order($attributes);
        $this->assign('order',$order);

        //默认收货地址
        if(is_null($info['addressid'])){
            $defaultAddress = UserAddress::where(['openid'=>$this->openid,'isdefault'=>1])->find();
        } else{
            $defaultAddress = UserAddress::where('id',$info['addressid'])->find();
        }
        $this->assign('defaultAddress',$defaultAddress);

        $prepayId = '';
        $result = $payment->prepare($order);
//        return json($result);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
        }
        if(!empty($prepayId)){
            $config = $payment->configForPayment($prepayId);
            $js = $this->app->js;
//        return json($config);
            $this->assign('config',$config);
            $this->assign('js',$js);
            return $this->fetch();
        } else{
            $error = $result->err_code_des;
            $this->assign('error',$error);
            return $this->fetch('error');
        }


    }


    /*
     * 支付回调
     */
    public function callback()
    {
        {
            $response = $this->app->payment->handleNotify(function($notify, $successful){
                // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
                $order = $this->model->where('trade_no',$notify->out_trade_no)->find();
                if (empty($order)) { // 如果订单不存在
                    Log::record('订单不存在');
                    return '订单不存在.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
                }
                // 如果订单存在
                // 检查订单是否已经更新过支付状态
                if (!is_null($order['paid_time'])) { // 假设订单字段“支付时间”不为空代表已经支付
                    return true; // 已经支付成功了就不再更新了
                }
                // 用户是否支付成功
                if ($successful) {
                    Log::record('确定已经支付成功了的');
                    // 不是已经支付状态则修改为已经支付状态
                    $Fields = ['paid_time'=>time(),'status'=>1,'openid'=>$notify->openid,'from'=>get_from_id($notify->openid)];
                    //修改订单状态
                    $result = $this->model->where('trade_no',$notify->out_trade_no)->setField($Fields);
                    Log::record($result);
                } else { // 用户支付失败
                    Log::record('用户支付失败');
                    $this->model->where('trade_no',$notify->out_trade_no)->setField(['status'=>2]);
                }
                return true; // 返回处理完成
            });
            return $response;
        }
    }


    /*
     * 选择收货地址
     */
    public function checkaddress($orderid)
    {
       if(request()->isPost()){
           $model = new \app\common\model\Order();
           $data = request()->post();
           if($model->where('id',$orderid)->setField('addressid',$data['addressid'])){
               return json(['code'=>2017,'msg'=>'设置成功，即将跳转支付页面']);
           } else {
               return json(['code'=>2013,'msg'=>'设置失败']);
           }
       } else{
           $model = new UserAddress();
           $address_list = $model->where('openid',$this->openid)->select();
           $this->assign('address_list',$address_list);
           //订单ID
           $this->assign('orderid',$orderid);
           return $this->fetch();
       }
    }

    public function shouquan($target_url='')
    {
        $oauth = $this->app->oauth;
        // 未登录
        if (empty($_SESSION['wechat_user'])) {
            $_SESSION['target_url'] = $target_url;
            return $oauth->redirect();
            // 这里不一定是return，如果你的框架action不是返回内容的话你就得使用
            // $oauth->redirect()->send();
        }
        // 已经登录过
        $user = $_SESSION['wechat_user'];
    }

    public function detail($id)
    {
        $result = $this->model->where('id',$id)->find();
        $this->assign('info',$result);

        $adressmodel  = new UserAddress();
        $addressinfo = $adressmodel->where('id',$result['addressid'])->find();

        $this->assign('addressinfo',$addressinfo);
        return $this->fetch();

    }
}
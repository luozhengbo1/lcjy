<?php
/**
 * Created by PhpStorm.
 * User: ran
 * Date: 2017/8/28
 * Time: 下午3:43
 */

namespace app\wechat\controller;


use app\common\model\Dealer;
use app\common\model\OrderFenyong;
use app\common\model\TixianLog;
use EasyWeChat\Foundation\Application;
use think\Config;
use think\Controller;
use think\Log;
use think\Session;

class Shangjia extends Controller
{
    protected $option;
    protected $app;
    protected $get_access_token;
    protected $openid;
    protected $model;
    protected $id;
    protected $config;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new Dealer();
        $this->option = Config::get('wechat');
        $this->app = new Application($this->option);
        $this->get_access_token = get_wechat_token();

        $this->openid = Session::get('weixin_user_openid');
        $this->id = get_dealerID_by_openid($this->openid);

        //判断openid是否为空，为空则获取当前方法的参数和方法名，存储缓存中
        //要求重新授权，同时判断是否已经注册，注册了则跳转之原来的方法页面，否则跳转到注册页面
        if(empty($this->openid)){
            $oauth = $this->app->oauth;
            //获取当前方法的参数，组合当前方法，然后存储到session中
            $param = http_build_query(request()->param());
            session('target_url',$_SERVER['PATH_INFO'].'?'.$param);

            $user = Session::get('weixin_user');
            if (empty($user)){
                $oauth->redirect('http://t.j9zz.com/wechat/wechat2')->send();
            }
        }else{
            if(!check_openid_shangjia($this->openid)){
                return $this->redirect('/wechat/register/shangjiaregister');
            }
        }

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
                'cert_path' => 'extend/cert/apiclient_cert.pem', // XXX: 绝对路径！！！！
                'key_path' => 'extend/cert/apiclient_key.pem',      // XXX: 绝对路径！！！！
                'notify_url' => 'http://t.j9zz.com/wechat/callback/index',       // 你也可以在下单时单独设置来想覆盖它

            ]];
        $this->app = new Application($this->config);
    }

    /** 商家个人中心
     * @return mixed
     */
    public function profile()
    {
        //今日提成
        $map =[];
        $map['dealer_id'] = $this->id;
        $getDate= date("Y-m-d");
        $today_s = strtotime(date("Y-m-d 00:00:00"));

        $sum = OrderFenyong::where($map)->whereTime('paid_time','between',[$today_s,time()])->sum('fenyong');
        $this->assign('sum',$sum?:0);

        //余额
        $amount = Dealer::where('id',$this->id)->value('amount');
        $this->assign('amount',$amount);
        return $this->fetch();
    }

    /** 余额
     * @return mixed
     */
    public function yue()
    {
        $amount = Dealer::where('id',$this->id)->value('amount');
        $this->assign('amount',$amount);

        $order_list = OrderFenyong::where('dealer_id',$this->id)->field('title,id,fenyong,paid_time')->select();
        $this->assign('order_list',$order_list);

        return $this->fetch();

    }

    /** 订单列表
     * @param int $type（1-全部，2-今日，3-一周，4-一个月，5-三个月）
     * @return mixed
     */
    public function order($type = 1)
    {
        $map = [];
        $condition = '';
        $time_s = strtotime("2017-01-01 00:00:00");
        switch ($type){
            case 2:
                $time_s = strtotime(date("Y-m-d 00:00:00"));
                break;
            case 3:
                $time_s = strtotime("last weeks");
                break;
            case 4:
                $time_s = strtotime("last month");
                break;
            case 5:
                $time_s = strtotime("-3 months");
                break;
        }
        $order_list = OrderFenyong::where('dealer_id',$this->id)->whereBetween('paid_time',[$time_s, time()])->select(); // 列表
        $sumforyongjin = OrderFenyong::where('dealer_id',$this->id)->whereBetween('paid_time',[$time_s, time()])->sum('fenyong');// 佣金
        $totalfororders = OrderFenyong::where('dealer_id',$this->id)->whereBetween('paid_time',[$time_s, time()])->count('fenyong');

        $this->assign('sumforyongjin',$sumforyongjin?:0);
        $this->assign('totalfororders',$totalfororders);
        $this->assign('type',$type);

        $this->assign('order_list',$order_list);

        return $this->fetch();
    }

    /** 提现申请
     * @return mixed|\think\response\Json
     */
    public function tixian()
    {
       if(request()->isPost()){
           $data = request()->post();
            $model = new TixianLog();
            if($model->allowField(true)->save($data)){
                $this->weixinPay();
                return json(['error'=>0,'msg'=>'提交成功']);
            } else {
                return json(['error'=>1,'msg'=>'提交失败']);
            }
       } else {
           $info = $this->model->where('id',$this->id)->field('id,amount,is_personnal,banknumber')->find();
           $this->assign('info',$info);

           return $this->fetch();
       }
    }

    /** 体现成功提示
     * @return mixed
     */
    public function txsuccess()
    {
        return $this->fetch();
    }

    /** 提现失败提示
     * @return mixed
     */
    public function txfailed()
    {
        return $this->fetch();
    }

    protected function weixinPay()
    {
        $merchantPay = $this->app->merchant_pay;
        $merchantPayData = [
            'partner_trade_no' => time(), //随机字符串作为订单号，跟红包和支付一个概念。
            'openid' => $this->openid, //收款人的openid
            'check_name' => 'NO_CHECK',  //文档中有三种校验实名的方法 NO_CHECK OPTION_CHECK FORCE_CHECK
            're_user_name'=>'张三',     //OPTION_CHECK FORCE_CHECK 校验实名的时候必须提交
            'amount' => 100,  //单位为分
            'desc' => '佣金提现',
            'spbill_create_ip' => request()->ip(),  //发起交易的IP地址
        ];
        $result = $merchantPay->send($merchantPayData);
        Log::record('提现结果:'.$result);
    }
}
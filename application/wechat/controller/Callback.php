<?php
/**
 * Created by PhpStorm.
 * User: ran
 * Date: 2017/8/17
 * Time: 下午3:43
 */

namespace app\wechat\controller;


use app\common\model\Dealer;
use app\common\model\OrderFenyong;
use EasyWeChat\Foundation\Application;
use think\Config;
use think\Controller;
use think\Log;

class Callback extends Controller
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
                'merchant_id'        => '1485409702',
//                'mch_id'        => '1485409702',
                'key'                => 'Gzlcjyjt20170725Liaoliutaocaozuo',
                'cert_path'          => '/extend/cert/apiclient_cert.pem', // XXX: 绝对路径！！！！
                'key_path'           => '/extend/cert/apiclient_key.pem',      // XXX: 绝对路径！！！！
                'notify_url'         => 'http://t.j9zz.com/wechat/callback/index',       // 你也可以在下单时单独设置来想覆盖它
            ]];
        $this->app = new Application(
            $this->config
        );
    }

    public function index()
    {
        $response = $this->app->payment->handleNotify(function ($notify, $successful) {
            Log::record($notify);
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = $this->model->where('trade_no', $notify->out_trade_no)->find();
            if (empty($order)) { // 如果订单不存在
                Log::record('订单不存在');
                return '订单不存在.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            //避免重复支付
            if(($order['paid_time'])){
                Log::record($notify->out_trade_no.'重复支付出错->'.$order['paid_time']);
                return false;
            }
            // 用户是否支付成功
            if ($successful) {
                Log::record('确定已经支付成功了的');
                // 不是已经支付状态则修改为已经支付状态
                $Fields = ['paid_time' => time(), 'status' => 1, 'openid' => $notify->openid, 'from' =>get_from_id($notify->openid)];
                //修改订单状态
                $result = $this->model->where('trade_no', $notify->out_trade_no)->setField($Fields);

                //分佣结算
                $orderinfo = $this->model->where('trade_no', $notify->out_trade_no)->find();
                if (get_from_id($notify->openid)) {

                    $orderFenyongModel = new OrderFenyong();

                    $data_result = [
                        'paid_time' => $Fields['paid_time'],
                        'trade_no' => $notify->out_trade_no,
                        'amount' => $orderinfo['amount'],
                        'user_id' => $orderinfo['openid'],
                        'num' => $orderinfo['num'],
                        'title' => $orderinfo['title'],
                        'goods_id' => $orderinfo['goods_id'],
                    ];
                    $data_result['dealer_id'] = $orderinfo['from'];
                    $data_result['from'] = $orderinfo['from'];
                    $data_result['fenyong'] = get_fenyong_amount(get_fenyong_percent($orderinfo['from']), $orderinfo['amount']); //是不是很长？？没关系，command+b
                    $orderFenyongModel->allowField(true)->save($data_result);

                    // 新增提成
                    $dealer = Dealer::get($orderinfo['from']);
                    $ticheng1 = Dealer::where('id',$orderinfo['from'])->setField('amount',$dealer['amount']+$data_result['fenyong']);
                    Log::record('经销商提成'+$ticheng1);

                    //存在一级分销商，上级商家添加提成金额并生成提成订单
                    if (check_dealer_id($orderinfo['from'])) {
                        Log::record('存在上级经销商->'.check_dealer_id($orderinfo['from']));

                        $data_result['dealer_id'] = check_dealer_id($orderinfo['from']);
                        //上级经销商的奋勇比例减去当前经销商的奋勇比例，再计算
                        $data_result['fenyong'] = get_fenyong_amount(
                            get_fenyong_percent(
                                check_dealer_id($orderinfo['from'])) - get_fenyong_percent($orderinfo['from']), $orderinfo['amount']
                        );
                        Log::record('上级经销商获得佣金->'.$data_result['fenyong']);

                        $addfenyongforshangji = OrderFenyong::create($data_result);
                        Log::record($addfenyongforshangji);

                        // 新增提成
                        $dealer = Dealer::get(check_dealer_id($orderinfo['from']));
                        $ticheng2= Dealer::where('id',check_dealer_id($orderinfo['from']))->setField('amount',$dealer['amount']+$data_result['fenyong']);
                        Log::record('一级经销商提成'+$ticheng2);
                    }

                } else { // 用户支付失败
                    $this->model->where('trade_no', $notify->out_trade_no)->setField(['status' => 2]);
                }
                return true; // 返回处理完成
            };
        });
        return $response;
    }
}
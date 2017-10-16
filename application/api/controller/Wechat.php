<?php
/*/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/1
 * Time: 14:45
 */

namespace app\api\controller;

use app\common\controller\ApiBase;
use app\common\controller\WechatBase;
use Doctrine\Common\Cache\RedisCache;
use EasyWeChat\Message\Text;
use EasyWeChat\Foundation\Application;

use think\Config;
use think\Controller;
use think\Session;

class Wechat extends ApiBase
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
        $this->openid = Session::get('openid');

    }

    public function server()
    {
        get_wechat_token();
        $this->menu();
        $server = $this->app->server;
        $server->setMessageHandler(function ($message) {
            $user_openid = $message['FromUserName'];
            Session::set('openid',$user_openid);
            if ($message->MsgType == 'event') {
                //来源
//                $key = explode('.', $message->EventKey);
                switch ($message->Event) {
                    case 'SCAN':
                        if(check_openid($user_openid)) {
                            return "酱酒智造欢迎您回来。";
                        } else{
                            //如果存在来源记录则直接调用来源记录，否则记录当前来源
                            if(get_openid_from($user_openid)){
                                $from = get_openid_from($user_openid);
                            } else{
                                $from = $message->EventKey;  //eg:qrscene_10
                                $from = str_ireplace('qrscene_','',$from);
                                set_openid_from($user_openid,$from);
                            }
                            // 我记不住解析出来的地址格式了，不知道是不是qrscene—— 这个你要排查下。
                            Session::set('from',$message->EventKey);
//                            return "欢迎回来，点击这里<a href='http://t.j9zz.com/wechat/register?src=$from'>立即注册</a>";
                            return "欢迎回来，请点击这里<a href='http://t.j9zz.com/wechat/register?src=$from'>立即注册</a>";

                        }
                        break;
                    case 'subscribe':
                        //如果存在来源记录则直接调用来源记录，否则记录当前来源
                        if(get_openid_from($user_openid)){
                            $from = get_openid_from($user_openid);
                        } else{
                            $from = $message->EventKey;  //eg:qrscene_10
                            $from = str_ireplace('qrscene_','',$from);
                            set_openid_from($user_openid,$from);
                        }
                        if(!check_openid($user_openid)) {
                            $data =  "感谢您关注酱酒智造商城~\n";
                            $data .= "使用帮助:\n";
                            $data .= "1.点击底部【立即定制】联系在线客服；\n";
//                            $data .= "2.点击这里<a href='http://t.j9zz.com/wechat/register?src=$from'>立即注册</a>";
                            $data .= "2.点击这里<a href='http://t.j9zz.com/wechat/register?src=$from'>立即注册</a>";

                        }else{
                            $data =  "定制时尚，智造快乐\n";
                            $data .=  "酱酒智造欢迎您回来~\n";
                            $data .= "使用帮助:\n";
                            $data .= "1.点击底部【立即定制】联系在线客服；\n";
                        }

                        return $data;
                        break;
                    case 'CLICK':
                        Session::set('weixin_user_openid',$user_openid);
                        if(check_openid($user_openid)){
                            //
                            $message =  new Text(['content' => '您好，有什么可以帮您？']);
                            $this->app->staff->message($message)->to($user_openid)->send();

                            $Transfer = new \EasyWeChat\Message\Transfer();
                            return [$Transfer];
                        } else {
                            $from = get_openid_from($user_openid);
//                            return "您必须先注册，点击这里<a href='http://t.j9zz.com/wechat/register?src=$from'>立即注册</a>";
                            return "您必须先注册，点击这里<a href='http://t.j9zz.com/wechat/register?src=$from'>立即注册</a>";
                        }
                        break;

                }
            } else {

                Session::set('weixin_user_openid',$user_openid);
                if(check_openid($user_openid)){
                    $message =  new Text(['content' => '您好，有什么可以帮您？']);
                    $this->app->staff->message($message)->to($user_openid)->send();

                    $Transfer = new \EasyWeChat\Message\Transfer();
                    return [$Transfer];
                } else {
                    $from = get_openid_from($user_openid);
                    return "您必须先注册，点击这里<a href='http://t.j9zz.com/wechat/register?src=$from'>立即注册</a>";
                }
            }
            return new \EasyWeChat\Message\Transfer();
        });
        $server->serve()->send();
    }


    public function getQcode($from = 0, $goods_id = 0)
    {
        $access_token = get_wechat_token();
        $url_get = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token;
        $data = [
            'action_name' => 'QR_LIMIT_STR_SCENE',
            'action_info' => [
                'scene' => [
                    'scene_str' => $from . $goods_id
                ]
            ]
        ];
        $data = \GuzzleHttp\json_encode($data);
        return json(http_post_data($url_get, $data)[1]);
    }

    /*
     * 获取二维码
     */
    public function Qcode($from='',$id = 0)
    {
        $qrcode = $this->app->qrcode;
        $result = $qrcode->forever($from,$id);// 或者 $qrcode->forever("foo");
        $ticket = $result->ticket; // 或者 $result['ticket']
        $url = $qrcode->url($ticket);
        return $url;
    }

    public function getFrom()
    {
        $user_openid = Session::get('openid');
        $from = get_openid_from($user_openid);
        return $from?$from:0;
    }
    /*
     * 授权回掉
     */
    public function callback()
    {
        $oauth = $this->app->oauth;
        $user = $oauth->user();
        session('wechat_user', $user->toArray());
        session('weixin_user_openid', $user->getId());

        $targetUrl = session('target_url');
        return $this->redirect($targetUrl);
    }

    /*
     * （微信）分享到朋友
     */
    public function share($share_url='')
    {
        $js = $this->app->js;
        $js->setUrl(urldecode($share_url));
        $data = $js->config(array('onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareAppMessage','onMenuShareTimeline'), $debug = false, $beta = false, $json = false);
        return json($data);
    }

    /*
     * 自定义菜单
     */
    public function menu()
    {
        $menu = $this->app->menu;
        $buttons = [
            [
                "type" => "click",
                "name" => "立即定制",
                "key" => "contact"
            ],
            [
//                "name" => "活动信息",
                "name" => "模板展示",
                "sub_button" => [
//                    [
//                        "type" => "view",
//                        "name" => "免费设计",
//                        "url" => "http://t.j9zz.com/special/fontout"
//                    ],
                    [
                        "type" => "view",
                        "name" => "产品展示",
                        "url" => "http://t.j9zz.com/special/product"
                    ]
                ],
            ],
            [
                "name" => "个人中心",
                "sub_button" => [
//                    [
//                        "type" => "view",
//                        "name" => "我的订单",
//                        "url" => "http://t.j9zz.com/wechat/user/myorder.html"
//                    ],
                    [
                        "type" => "view",
                        "name" => "个人信息",
                        "url" => "http://t.j9zz.com/wechat/user/userinfo"
                    ],
//                    [
//                        "type" => "view",
//                        "name" => "收货地址",
//                        "url" => "http://t.j9zz.com/wechat/user/myaddress.html"
//                    ],
//                    [
//                        "type" => "view",
//                        "name" => "商户入口",
//                        "url" => "http://t.j9zz.com/wechat/shangjia/profile.html"
//                    ],
                ],
            ],
        ];
        $menu->add($buttons);
        $menus = $menu->all();
        return json($menus);
    }

    public function _empty()
    {
        return 'Forbidden...';
    }

    public function send()
    {

    }
}
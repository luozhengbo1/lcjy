<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/2
 * Time: 15:36
 */

namespace app\wechat\controller;


use app\common\controller\WechatBase;
use app\common\model\SmsLog;
use app\common\model\UserAddress;
use EasyWeChat\Foundation\Application;
use think\Config;
use think\Log;
use think\Session;

class User extends WechatBase
{

    protected $model;
    protected $config;
    protected $app;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\User();
//        $this->assign('userinfo',$this->check());
    }

    public function register()
    {
        $this->_initialize();

    }


    public function profile()
    {
        $oauth = $this->app->oauth;
        // 获取 OAuth 授权结果用户信息
        $user = $oauth->user();
        $_SESSION['wechat_user'] = $user->toArray();
        Log::record($user);
        $targetUrl = empty($_SESSION['target_url']) ? '/' : $_SESSION['target_url'];
        header('location:' . $targetUrl); // 跳转到 user/profile
    }

    /*
     * 个人中心
     */
    public function userinfo()
    {
        $model = new \app\common\model\User() ;
        $user = $model->where('openid',$this->openid)->find();
 		$a = get_openid_from($this->openid);
           if( empty($user) ){
           	   header("location:/wechat/register?src=$a" );
           }
        $this->assign('userinfo',$user);
        return $this->fetch('profile');
    }
    /*
     * 编辑个人资料
     */
    public function edituserinfo()
    {
        $model = new \app\common\model\User() ;
        if(request()->isPost()){
            $data = request()->post();
        } else{
            $user = $model->where('openid',$this->openid)->find();
            $this->assign('userinfo',$user);
            return $this->fetch('editprofile');
        }
    }
    /*
     * 添加收货地址
     */
    public function myaddress()
    {

        if(request()->isPost()){
            $data = request()->post();
            $data['openid'] = $this->openid;
            //取消其他默认地址
            if(isset($data['isdefault']) && !is_null($data['isdefault']))
            {
                UserAddress::where('isdefault',1)->setField('isdefault',0);
            }
            $Model = new UserAddress($data);
            if($Model->save()){
                $data['id'] = $Model->id;
                return json(['code'=>2001,'msg'=>'添加成功','data'=>$data]);
            }
        } else {
            $address_list = UserAddress::where('openid',$this->openid)->order('isdefault Desc')->select();

            $this->assign('random',time());
            $this->assign('address_list',$address_list);
            return $this->fetch();
        }
    }

    /*
     * 添加收货地址
     */
    public function addAddress()
    {
        if(request()->isPost()){
            $data = request()->post();
            $data['openid'] = $this->openid;
            $data['isdefault'] = 1;
            //取消其他默认地址
            if(isset($data['isdefault']) && !is_null($data['isdefault']))
            {
                UserAddress::where(['isdefault'=>1,'openid'=>$this->openid])->setField('isdefault',0);
            }
            $area = $data['area'];
            if(!empty($area)){
                $area = explode(',',$area);
                $data['province'] = $area[0];
                $data['city'] = $area[1];
                if(isset($area[2])){
                    $data['country'] = $area[2];
                }
            }
            $Model = new UserAddress();
            if($Model->allowField(true)->save($data)){
                $data['id'] = $Model->id;
                return json(['code'=>0,'msg'=>'添加成功','data'=>$data]);
            } else{
                return json(['code'=>1,'msg'=>'添加失败，稍后重试','data'=>$data]);
            }
        } else {
            return $this->fetch();
        }
    }

    /*
     * 删除收货地址
     */
    public function deleteAddress($id)
    {
        $model = new UserAddress();
        if($model->where('id',$id)->delete()){
            return json(['code'=>2017,'msg'=>'删除成功']);
        } else {
            return json(['code'=>2013,'msg'=>'删除失败']);
        }
    }

    public function close()
    {
        return $this->fetch();
    }

    /*
     * 设置为默认地址
     */
    public function setDefaultAddress($id)
    {
        if(request()->isPut()){
            $model = new UserAddress();
            $current = $model->where(['id'=>$id,'isdefault'=>1])->find();
            if($current){
                return json(['code'=>'2016','msg'=>'设置失败']);
            } else{
                $model->where('isdefault',1)->setField('isdefault',0);
                if($model->where('id',$id)->setField('isdefault',1)){
                    return json(['code'=>'2001','msg'=>'设置成功']);
                } else{
                    return json(['code'=>'2016','msg'=>'设置失败,系统出错']);
                }
            }
        }
    }

    public function editaddress($id)
    {
        $model = new UserAddress();

        if($this->request->isPost()){

            $data = request()->param();
            $data['isdefault'] = 1;
            //取消其他默认地址
            if(isset($data['isdefault']) && !is_null($data['isdefault']))
            {
                UserAddress::where(['isdefault'=>1,'openid'=>$this->openid])->setField('isdefault',0);
            }

            $area = $data['area'];
            if(!empty($area)){
                $area = explode(',',$area);
                $data['province'] = $area[0];
                $data['city'] = $area[1];
                if(isset($area[2])){
                    $data['country'] = $area[2];
                }
            }

            if ($model->allowField(true)->save($data,$id)){
                return json(['code'=>0,'msg'=>'修改地址成功']);
            } else {
                return json(['code'=>1,'msg'=>'修改内容失败']);
            }
        } else{
            $info = $model->where('id',$id)->find();
            $this->assign('info',$info);
            return $this->fetch();
        }

    }
    /*
     * 我的订单
     */
    public function myorder()
    {
        $model = new \app\common\model\Order();
        $order_list = $model->where('openid',$this->openid)->select();
        $this->assign('order_list',$order_list);
        return $this->fetch();
    }

    /*
     * 商家个人中心
     */
    public function sj_userinfo()
    {
        return $this->fetch('shangjia/profile');
    }



}
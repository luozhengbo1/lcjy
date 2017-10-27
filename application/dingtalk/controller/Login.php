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
use Dingtalk\Utils\Http;
use Dingtalk\Utils\Cache;
use Dingtalk\Api;
use think\Session;

class Login extends Controller
{

    protected $corpid = 'ding38872dd0a69908aa35c2f4657eb6378f';
    protected $corpsecret = 'BBewgd8BudgGQJPjpbYRfN9jmc9aDKU6lxucRjK2NEr5c8ei3HUV_NQaDpa_-Qzv' ;
    protected $unionid = 'BBewgd8BudgGQJPjpbYRfN9jmc9aDKU6lxucRjK2NEr5c8ei3HUV_NQaDpa_-Qzv' ;

    public function callback()
    {
        if(request()->isPost()){
           return json(Dingtalk::getConfig());
        } else {
            $code = request()->param('code');
//			var_dump($code);
            $sns = new Sns();
            $result = $sns->getSnsPersistentCode($code);
//          var_dump($result);
            $this->assign('config',json_decode(Dingtalk::getConfig(),true));
            return $this->fetch();
        }
    }
    /**	
     *  显示需要审批的订单
     */
    public function initiatingOrder()
    {
        $orderModel = new \app\common\model\Order();
        $order_list  = $orderModel
         ->where(['os_order.status'=>1])
         ->order('id DESC')
         ->select();

    	return $this->fetch('initiatingOrder', ['order_list' => $order_list]);
    }
    
 
    /**
     * 接受前段请求的数据
     * @return 
     */
    public function getOrderData($id,$trade_no)
    {
    	$orderModel = new \app\common\model\Order();
		$map['id'] = $id;
		$map['trade_no'] = $trade_no;
         $orderDetial = $orderModel
         ->where($map)
         ->order('id DESC')
         ->find();
       return json_encode($orderDetial) ;
    }
    
    /**	
     * 获取accesstoken
     * 
     */
    public function getAccessToken()
    {

 	 	$accessToken = Cache::get('access_token', '');
        if (!$accessToken)
        {
           	$response = Http::get('/gettoken', array('corpid' => $this->corpid, 'corpsecret' => $this->corpsecret));
            $accessToken = $response->access_token;
            Cache::set('accessToken', $accessToken,'');
        }
        return $accessToken;

    }

    /**
     *
     * 获取成员详情
     * Https请求方式: GET
     * https://oapi.dingtalk.com/user/get?access_token=ACCESS_TOKEN&userid=zhangsan
     */
    public function getUserDetail()
    {

           $userDetail =  Http::get('/user/get', 
                array(
                    'access_token'=> $this->getAccessToken() , 
                    'userid'=>$this->getUserId()
                )
            );
    
           print_r($userDetail);
    }


    /**
     *获取管理员列表
     * https://oapi.dingtalk.com/user/get_admin?access_token=ACCESS_TOKEN
     */
    public function getAdmin()
    {
         $adminList = Http::get(
            '/user/get_admin', 
            array('access_token' => $this->getAccessToken())
            );   
      	if($adminList->errcode==0 && $adminList->errmsg=="ok" ){
            $adminListAll =  $this->object2array( $adminList);
            // 管理人员列表
     		return json_encode($adminListAll['adminList']);
      	}else{
      		return false;
      	}
    }
   /**
    * 对象转为数组
    * @param  [type] $object [description]
    * @return [type]         [description]
    */
    public function object2array($object) 
    {
      if (is_object($object)) {
        foreach ($object as $key => $value) {
          $array[$key] = $value;
        }
      }
      else {
        $array = $object;
      }
      return $array;
    }
}



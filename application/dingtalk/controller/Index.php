<?php
namespace app\dingtalk\controller;

use Dingtalk\Utils\Http;
use Dingtalk\Utils\Cache;
use think\Session;

require_once 'DingTalkClient.php';
require_once 'SmartworkBpmsProcessinstanceCreateRequest.php';
require_once 'FormComponentValueVo.php';
require_once 'ResultSet.php';
require_once 'RequestCheckUtil.php';
require_once 'TopLogger.php';
class Index{
	
	protected static $web="http://t.j9zz.com";
	/**	
	 * 获取钉钉开放应用的ACCESS_TOKEN
     * @return  string   
	 */
    public static function getAccessToken()
    {
        $accessToken = Cache::get('DING_smlogin_access_token',false);
        if (!$accessToken)
        {
            $appid = 'dingoa70ok7bqooyhq9ttl';
            $appsecret = 'dBr-XDEudfYd-GA2ie3X-MIvxOgC-A6Vx1EBTHq_iO6T6o2scWqpZtKcBf7mh3QZ';
            $response = Http::get('/sns/gettoken', array('appid' => $appid, 'appsecret' => $appsecret));
            $accessToken = $response->access_token;
            Cache::set('DING_smlogin_access_token', $accessToken,false);
        }
        return $accessToken;

    }
    
    
    /**
     * 获取永久授权码
     * @param $accessToken
     * @param $opt ["tmp_auth_code": "xxxxx"] //临时授权码
     * @return string
     */
    public static function getPersistentCode( $opt)
    {
        $response = Http::post('/sns/get_persistent_code',
            ['access_token'=>$this->getGlobalAccessToken() ],
            json_encode($opt));
//      Session::set('unionid', $response->unionid, false );

        return $response;
    }

    /**
     * 获取用户授权的SNS_TOKEN
     * @param $accessToken
     * @param $opt
     * @return string
     */
    public static function getSnsToken($accessToken, $opt)
    {
        $response = Http::post('/sns/get_sns_token',
            ['access_token'=>$accessToken],
            json_encode($opt));
        return $response->sns_token;
    }

    /**
     * 获取用户授权的个人信息
     * @param $snsToken
     * @return string
     */
    public static function getUserInfo($snsToken)
    {
        $response = Http::get('/sns/getuserinfo', ['sns_token'=>$snsToken]);
        return $response->user_info;
    }

    /**
     * 获取用户信息
     * @param  [type] $tmpCode [description]
     * @return json          用户详情
     */
    public  function getUser($tmpCode)
    {
        $userinfo = Session::get('dingding_user',false);
        if( !$userinfo ){
             $accessToken = self::getAccessToken();
            $persistentCode = self::getPersistentCode($accessToken,['tmp_auth_code'=>$tmpCode]);
            $snsToken = self::getSnsToken($accessToken,
                [
                    "openid"=>$persistentCode->openid,
                    "persistent_code"=>$persistentCode->persistent_code
                ]);
            $userinfo = self::getUserInfo($snsToken);
            Session::set('dingding_user', $userinfo,false);
        }
        return $userinfo;
    }
    
    /**	
     * 获取用户信息
     */
    public function userinfo()
    {
    	$code =  $_GET['code'];
    	$corpid =  $_GET['corpid'];
    	$res = Http::get('/user/getuserinfo', 
    		array(
    			'access_token'=>$this->getGlobalAccessToken(),
    			'code' => $code
    		)
    	);
        Cache::set('userId',$res->userid,false);
    	return $res;
    }

 	/**	
     * 获取全局accesstoken
     * 
     */
    public function getGlobalAccessToken()
    {

   	 	 $accessToken = Cache::get('access_token', '');
           if (!$accessToken)
           {
           	$response = Http::get('/gettoken', array('corpid' => 'ding38872dd0a69908aa35c2f4657eb6378f', 'corpsecret' => 'BBewgd8BudgGQJPjpbYRfN9jmc9aDKU6lxucRjK2NEr5c8ei3HUV_NQaDpa_-Qzv'));
            $accessToken = $response->access_token;
               Cache::set('accessToken', $accessToken,'');
           }
        return $accessToken;

    }


	/**	
	 * 
	 * 获取用户详细信息
	 */
	public function getUserDetail()
	{
		$userId =  Cache::get('userId',false) ;
    	$res = Http::get('/user/get', 
    		array(
    			'access_token'=>$this->getGlobalAccessToken(),
    			'userid' => $userId
    		)
    	);
    	return json_encode($res);
	}
	
	/**	
	 * 获取部门下的所有的成员
	 */
	public function getDepartmen()
	{
		$res = Http::get('/user/list', 
    		array(
    			'access_token'=>$this->getGlobalAccessToken(),
    			'department_id' => 45460254
    		)
    	);
    	if($res->errcode==0 && $res->errmsg=='ok'){
    			return $res->userlist;
    	}
    	return false; 
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
    public static function getOrderData($id,$trade_no)
    {
    	$orderModel = new \app\common\model\Order();
		$map['id'] = $id;
		$map['trade_no'] = $trade_no;
        $sql ="select o.trade_no,o.amount,o.num,o.type,o.author,o.image,o.create_time,o.paid,o.paid_time,o.status,o.addressid,o.tel,o.order_user,g.title,g.yuanjiang from os_order as o, os_goods as g where o.goods_id=g.id and o.id=$id and o.trade_no=$trade_no ";
        $orderDetial = $orderModel->query($sql);
//      echo "<pre>";
//      	print_r($orderDetial[0]);
       	return $orderDetial[0] ;
    }

	
	//$url ： 接口地址 $param ： 上传数据
    static  function http_post($url,$param){           
         $oCurl = curl_init();           
         if(stripos($url,"https://")!==FALSE){                   
         curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);                   
         curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);           
         }          
         $strPOST = $param;          
         curl_setopt($oCurl, CURLOPT_URL, $url);           
         curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );           
         curl_setopt($oCurl, CURLOPT_POST,true);           
         curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);          
         $sContent = curl_exec($oCurl);           
         $aStatus = curl_getinfo($oCurl);           
         curl_close($oCurl);           
         if(intval($aStatus["http_code"])==200){                   
                return $sContent;           
         }else{                  
                 return false;          
         }    
     }
	
	/**
	 * 上传图片
	 */
	public  function getUploadMediaId($image)
	{
		$type="image";
//		$image = $_GET['image'];
		$media['media'] =  "@".self::$web.$image.';type=image;filename=41d4a59bc3078148d8657a65fd65c780.jpg;filelength=960;content-type=image/jpg;';
		echo "<pre>";
			print_r($media['media']);
		echo "<hr>";
		$access_token = $this->getGlobalAccessToken();
//		var_dump($access_token);
		$url = 'https://oapi.dingtalk.com/media/upload';
		$url1 = 'https://oapi.dingtalk.com/media/upload?access_token='.$access_token.'&type=image';
		var_dump( self::http_post($url1, $media['media']) ) ;
		$res = Http::post($url, array('access_token'=>$access_token,'type'=>'image','media' =>  "@".self::$web.$image) );
		echo '<pre>';
		print_r($res);
	}
	
	 /**	
     * 发起审批
     */
    public function initiatingOrderStart($id, $trade_no )
    {
    	$orderDetail = self::getOrderData($id,$trade_no );
//		$res = $this->getUploadMediaId($orderDetail['image']);die;
   		$testList = $this->getDepartmen();
   		$test1 = $this->object2array($testList['1']);
   		$test2 = $this->object2array($testList['2']);
		$test3 = $this->object2array($testList['3']);
    	$oldUserDetail =  json_decode ( $this->getUserDetail() );
//  	var_dump($oldUserDetail);
    	// 获取用户详细信息
    	$userDetail =  $this->object2array($oldUserDetail);
		$dept_id = (string)$userDetail['department'][0];
   		$access_token = $this->getGlobalAccessToken();
	     $c = new \DingTalkClient;
	     $req = new \SmartworkBpmsProcessinstanceCreateRequest;
	     $req->setAgentId("122700771");
	    //process_code可在oa后台-审批-编辑表单-表单url中的processCode找到
	    //PROC-EF6YLU2SO2-BLFPND6JQ1N2UNBTGESY1-P9PKV39J-1
	     $req->setProcessCode("PROC-EF6YLU2SO2-BLFPND6JQ1N2UNBTGESY1-P9PKV39J-1");
	     //发起人ID
	     $req->setOriginatorUserId( $userDetail['userid']  );
	     //发起人部门ID
	     $req->setDeptId( $dept_id );
		//审批人userid列表
	     $req->setApprovers( $userDetail['userid'] );
	     //抄送人
//	     $req->setCcList("zhangsan,lisi");
	     $req->setCcPosition("START");
//		$form_component_values = new \FormComponentValueVo;
//		$form_component_values->name="单行输入框";
//		$form_component_values->value="事假";
//		$form_component_values->name1="明细";
//		$form_component_values->value1=array( array(  array('name'=>"单行输入框2",'value'=>"test")) )  ;
//		$form_component_values->name2="图片";
//		$form_component_values->value2=array('http://t.j9zz.com/public/uploads/20170809/8a1fcb750338d5825b6fa731d9d7b2a0.jpg') ;
		//$form_component_values->ext_value="总天数:1";
		$data = [
			[
				'name'=>'订单编号',
				'value'=>$orderDetail['trade_no']?$orderDetail['trade_no']:'无'
			],
			[
				'name'=>'产品名称',
				'value'=>$orderDetail['title']?$orderDetail['title']:'无'
			],
			[
				'name'=>'客户姓名',
				'value'=>$orderDetail['paid']?$orderDetail['paid']:'无'
			],
			[
				'name'=>'联系电话',
				'value'=>$orderDetail['tel']?$orderDetail['tel']:'无'
			],
			[
				'name'=>'QQ/微信号',
				'value'=>$orderDetail['paid']?$orderDetail['paid']:'无'
			],
			[
				'name'=>'收货地址',
				'value'=>'18285111561'
			],
			[
				'name'=>'下单日期',
				'value'=>'18285111561'
			],
			[
				'name'=>'类别',
				'value'=>'18285111561'
			],
			[
				'name'=>'酒体规格',
				'value'=>'18285111561'
			],
			[
				'name'=>'数量',
				'value'=>$orderDetail['num']?$orderDetail['num']:'无'
			],
			[
				'name'=>'包装规格',
				'value'=>'18285111561'
			],
			[
				'name'=>'瓶标',
				'value'=>'18285111561'
			],
			[
				'name'=>'瓶型',
				'value'=>'18285111561'
			],
			[
				'name'=>'盒型',
				'value'=>'18285111561'
			],
//			[
//				'name'=>'明细',
//				 "value"=>
//				 [
//				 	[
//				 		['name'=>'单行输入框2','value'=>'test']
//				 	]
//				 ]
//			],
			[
				'name'=>'图片',
				'value'=>[self::$web.$orderDetail['image']],
			]
		];
	     $req->setFormComponentValues(json_encode($data));
	     $resp = $c->execute($req, $access_token);
	     return ($resp->result->is_success=='true' )? '审批发起成功': false;
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
    
    
    /**	
     * 上传图片或者其他文件的接口
     */
    public function uploadMediaId($media, $type='image')
    {
    	$access_token = $this->getGlobalAccessToken();
		$res =Http::post('/media/upload',
			array('access_token'=>$access_token,'type'=>$type,'media'=>$media)
		);
		if($res->errcode==0 && $res->errcode=='ok'){
			return $res->media_id;
		}
		return false;
    	
    }
    
    /**	
     * Https请求方式: GET
     *https://oapi.dingtalk.com/department/list?access_token=ACCESS_TOKEN 
     */
    public function getDepartmentList()
    {
    	$access_token = $this->getGlobalAccessToken();
    	$departmentList =Http::post('/media/upload',array('access_token'=>$access_token) );
    	echo "<pre>";
    	print_r($departmentList);
    }
    
    
    /**	
     * 获取审批数据
     */
    public function getinitiatingOrder()
    {
    	$c = new \DingTalkClient;
		$req = new \SmartworkBpmsProcessinstanceListRequest;
		$req->setProcessCode("PROC-FF6YR2IQO2-NP3LJ1J0SO4182NKX26K3-3N23J-PB");
		$req->setStartTime("1496678400000");
		$req->setEndTime("1496678400000");
		$req->setSize("10");
		$req->setCursor("0");
		$resp = $c->execute($req, $access_token);
    }
	
}
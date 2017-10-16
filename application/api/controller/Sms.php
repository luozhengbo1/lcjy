<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/7/31
 * Time: 13:44
 */

namespace app\api\controller;


use app\common\controller\ApiBase;
use app\common\model\SmsLog;
use PFinal\Aliyun\AliyunSMS;
use think\Controller;

class Sms extends ApiBase
{
    protected $accessId;
    protected $accessKey;
    protected $endPoint;
    protected $topicName;
    protected $signName;
    protected $sms;
    public function __construct()
    {
        $this->sms = new AliyunSMS();
        $this->sms->accessKeyId = config('aliyunsms.accessId');
        $this->sms->accessKeySecret =  config('aliyunsms.accessKey');
        $this->sms->signName = config('aliyunsms.signName');
        $this->sms->templateId = config('aliyunsms.templateId');
        $this->sms->templateCodeKey = 'number';

    }

    public function sendCode($mobile,$token=0,$type) {
        $sms_type =['1'=>'SMS_78605132'];
        //todo:添加更多模板类型
        if ($token === config('aliyunsms.token')){
            $code = mt_rand(1000,9999);

            $sms_id = 0;

            if( $this->sms->sendCode($mobile, $code) ){
                //储存验证码
                $sms_model = new SmsLog();
                $data = ['code'=>$code,'create_time'=>time(),'type'=>$type,'mobile'=>$mobile];

                if ($sms_model->allowField(true)->save($data)){
                    $sms_id = $sms_model->sms_id;
                }
                return json(['code'=>2000,'msg'=>'success','sms_id'=>$sms_id]);
            } 
            else {
                return json(['code'=>2004,'msg'=>'failed','sms_id'=>$sms_id]);
            }
        }else{
            echo '非法请求';
        }
    }
    public function test($mobile,$token=0,$type) {
        $sms_type =['1'=>'SMS_78605132'];
        //todo:添加更多模板类型
        if ($token === config('aliyunsms.token')){
            $code = mt_rand(1000,9999);
            $aliyunSms = new \Oakhope\AliyunSMS($this->accessId, $this->accessKey, $this->endPoint, $this->topicName, $this->signName);

            $aliyunSms->sendOne('SMS_78605132', $mobile, ['number' => $code]);
        }else{
            echo '非法请求';
        }
    }


    public function verify($sms_id){
        $result = SmsLog::where('sms_id',$sms_id)->value('code');
        if($result){
            $code = $result;
        } else {
            $code = md5(time().config('salt'));
        }
        return $code;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: ran
 * Date: 2017/8/18
 * Time: 下午3:43
 */

namespace app\tools\controller;

use app\common\model\H5Qcode;
use think\Controller;

class Qcode extends Controller
{
    protected $model;
    protected $apikey;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new H5Qcode();
        $this->apikey = '20170818146625';
    }

    public function index()
    {
        return $this->fetch();
    }

    /*
     * 生成二维码
     */
    public function create()
    {
        if (request()->isPost()) {
            $qcode_url = request()->post('qcode_url');
            $remote_server = 'http://api.wwei.cn/dewwei.html';
            $qcode_url = 'http://t.j9zz.com/' . $qcode_url;
            $url = 'http://api.wwei.cn/dewwei.html';
            $post_data = array(
                'data' => $qcode_url,
                'apikey' => '20170818146625',
            );
            //解析二维码，
            $result = json_decode($this->send_post($url, $post_data), true);
            $this->model->weixin_url = $result['data']['raw_text']; // 真实的微信URL，用这URL生成新的二维码
            $create_result = json_decode($this->create_new_qcode($result['data']['raw_text']), true);

            $save_dir='public/uploads/qcode/';
            if ($create_result['status']) {
                $saveImg = getImage($create_result['data']['qr_filepath'],$save_dir);
                $this->model->qcode = $saveImg['save_path'];
                if ($this->model->save()) {
                    return json(['code' => 0, 'msg' => '生成成功', 'url' => 'http://t.j9zz.com/special/fontout?id=' . $this->model->id]);
                } else {
                    return json(['code' => 1, 'msg' => '生成失败', 'url' => '']);
                }
            } else{
                return json(['code' => 1, 'msg' => '二维码解析失败', 'url' => '']);
            }

        }
    }


    /*
     * 发送请求，获取真实的微信URL
     */
    function send_post($url, $post_data)
    {

        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                // 'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    /*
     * 生成新的二维码
     */
    function create_new_qcode($weixin_url)
    {
        $post_data = array(
            'data' => $weixin_url,
            'apikey' => $this->apikey,
            'version' => '1.0',
        );
        $url = 'http://api.wwei.cn/wwei.html';
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    /*
     * 获取二维码
     */
    public function get_code($id)
    {
        $qcode = $this->model->where('id',$id)->field('qcode,id,is_share')->find();
        if(!is_null($qcode)){
            return json(['code'=>0,'data'=>$qcode]);
        } else{
            return json(['code'=>2]);
        }
    }

    /*
     * 判断是否分享过，分享过则标记is_share字段为1
     */
    public function checkshare($id)
    {
        $share  = $this->model->where('id',$id)->field('is_share')->find();
        if($share['is_share'] ==1){
            return json(['code'=>0,'share'=>$share]);
        } else{
            $this->model->where('id',$id)->setField('is_share',1);
            return json(['code'=>1,'share'=>$share]);
        }
    }

}


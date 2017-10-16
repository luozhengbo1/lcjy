<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/7/31
 * Time: 17:31
 */

namespace app\api\controller;

use app\common\controller\ApiBase;
use app\common\model\GoodsCate;

class Goods extends ApiBase
{

    protected $model;
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Goods();
    }

    /*
     * 指定分类商品列表
     */
    public function lists($type=0)
    {
        $data = $this->model->where(['status'=>1,'type'=>$type])->field('id,title,image')->select();
        $baseUrl = 'http://t.j9zz.com/';
        if(!empty($data)){
            foreach ($data as $key=>$value){
                $data[$key]['image'] = $baseUrl.$value['image'];
            }
            return json(['code'=>2000,'msg'=>'产品列表加载成功','data'=>$data]);
        } else {
            return json(['code'=>2004,'msg'=>'该分类暂无产品']);
        }
    }

    public function info($id,$from='0')
    {
//        $from = request()->param('qrcode_id');
        $info = $this->model->where('id',$id)->field('id,image,about,title,type,yuanjiang,yuanjiang as yj')->find();
        $baseUrl = 'http://t.j9zz.com/';
        if(!empty($info)){
            $wechat = new Wechat();
            $info['image'] = $baseUrl.$info['image'];
            $info['qcode'] = $wechat->Qcode($from,$id);
            $info['about'] = $info['about']?:'暂无介绍';

            return json(['code'=>2000,'msg'=>'加载成功','data'=>$info]);
        } else {
            return json(['code'=>2004,'msg'=>'查询失败']);
        }
    }

    public function goods_cate()
    {
        $model = new GoodsCate();
        $data = $model->where('status',1)->limit(5)->order('id asc')->select();
        if(!empty($data))
        {
            return json(['code'=>2000,'msg'=>'加载成功','data'=>$data]);
        } else{
            return json(['code'=>2000,'msg'=>'加载失败','data'=>$data]);
        }
    }
}
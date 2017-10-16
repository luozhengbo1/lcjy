<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/1
 * Time: 09:30
 */

namespace app\api\controller;


use app\common\controller\ApiBase;
use think\Controller;



class H5contact extends ApiBase
{

    protected  $model;
    protected $validate;
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\H5Contact();
        $this->validate = new \app\api\validate\h5contact();
    }

    public function index()
    {
        return $this->fetch();
    }
    public function add()
    {

        if(request()->isPost()){
            $data = request()->post();
            $validate_result = $this->validate($data, 'h5contact');

            if ($validate_result !== true) {
               return json(['code'=>2002,'msg'=>$validate_result]);
            } else {
                if($this->model->allowField(true)->save($data)){
                    return json(['code'=>2001,'msg'=>'提交成功']);
                } else {
                    return json(['code'=>2004,'msg'=>'提交失败']);
                }
            }
        } else {
            return $this->fetch();
        }
    }

    /**
     * 活动详情
     * @param $id
     * @return \think\response\Json
     * @return
     */
    public function info($id)
    {
        $info = db('special')->where('id',$id)->find();
        if(!empty($info)){
            db('special')->where('id',$id)->setInc('views',1);
            $info['views'] =  $info['views'] +13066;
        }
        return json($info);
    }

    /*
     * 增加访问数
     */
    public function addviews($id,$step=0)
    {
        db('special')->where('id',$id)->setInc('views'.$step,1);
    }
}
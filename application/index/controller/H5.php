<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/7
 * Time: 10:58
 */

namespace app\index\controller;


use app\common\model\H5Contact;
use think\Controller;

class H5 extends Controller
{

    protected $model;
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new H5Contact();
    }

    public function users()
    {
        $data = $this->model->select();
        $this->assign('list',$data);
        return $this->fetch();
    }

    public function changestatus($id)
    {
        if($this->model->where('id',$id)->setField('status',1)){
            return json(['code'=>0,'msg'=>'修改成功']);
        } else {
            return json(['code'=>1,'msg'=>'修改失败']);
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: ran
 * Date: 2017/8/10
 * Time: 下午2:37
 */

namespace app\admin\controller;


use app\common\controller\AdminBase;
use think\Controller;

class Goodstype extends AdminBase
{
    protected $model;
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\GoodsType();
    }

    /*
     * 类型列表
     */
    public function index()
    {

        $data = $this->model->order('id DESC')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    /*
     * 添加类型
     */

    public function add()
    {
        if(request()->isPost()){

            $data = request()->post();

            if($this->model->allowField(true)->save($data)){
                return $this->success('添加成功');
            } else {
                return $this->error('添加失败');
            }
        } else{
            return $this->fetch();
        }
    }

    /*
     * 修改类型
     */
    public function edit($id)
    {
        if(request()->isPost()){
            $data = request()->post();
            if($this->model->allowField(true)->save($data,$id)){
                return $this->success('修改成功');
            } else {
                return $this->error('修改失败');
            }
        } else{
            $info = $this->model->where('id',$id)->find();
            $this->assign('info',$info);
            return $this->fetch();
        }
    }

    /*
     * 删除类型
     */
    public function delete($id)
    {
        if($this->model->where('id',$id)->delete())
        {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }


}
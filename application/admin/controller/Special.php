<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/1
 * Time: 16:59
 */

namespace app\admin\controller;


use app\common\controller\AdminBase;
use think\Controller;

class Special extends AdminBase
{

    protected $model;
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Special();
    }

    //专题列表
    public function index($keyword = '', $page = 1)
    {
        $map = [];
        if ($keyword) {
            $map['title|tel|address'] = ['like', "%{$keyword}%"];
        }
        $special_list = $this->model->where($map)->order('id DESC')->paginate(15, false, ['page' => $page]);

        return $this->fetch('index', ['special_list' => $special_list, 'keyword' => $keyword]);
    }

    /**
     * 添加活动
     * @return mixed
     * @return
     */
    public function add()
    {
        return $this->fetch();
    }
    /**
     * 保存活动
     * @return
     */
    public function save()
    {
        if (request()->isPost()){
            $data = request()->param();
            if($this->model-allowField(true)->save($data)){
                return $this->success('添加成功');
            } else {
                return $this->error('添加失败');
            }
        }
    }

    /*
     * 编辑专题
     */
    public function edit($id)
    {
        $info = $this->model->where('id',$id)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    /*
     * 保存修改
     */
    public function update($id)
    {
        if (request()->isPost()){
            $data = request()->param();
            if ($this->model->allowField(true)->save($data,$id)){
                return $this->success('修改成功');
            } else {
                return $this->error('修改失败');
            }
        }
    }

    /*
     * 删除活动
     */
    public function delete($id)
    {
        if($this->model->delete($id)){
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    /*
     * 活动反馈
     */
    public function feedback()
    {
        $data = db('H5Contact')->select();
        $this->assign('feedback_list',$data);
        return $this->fetch();
    }

    public function h5contact($page=1)
    {
        $info = $this->model->where('id',1)->find();
        $this->assign('info',$info);
        //跳出率
        $this->assign('close1',round(($info['views'] - $info['views1'])/$info['views'] * 100,2).'%');
        $this->assign('close2',round(($info['views'] - $info['views2'])/$info['views'] * 100,2).'%');
        $this->assign('close3',round(($info['views'] - $info['views3'])/$info['views'] * 100,2).'%');

        $data = db('H5Contact')->paginate(15, false, ['page' => $page]);
        $this->assign('feedback_list',$data);
        return $this->fetch();
    }

    public function feedbackDelete($id)
    {
        if(db('H5Contact')->where('id',$id)->delete()){
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    /*
     * 改变状态
     */
    public function feedbackcontact($id)
    {
        if(db('H5Contact')->where('id',$id)->setField('status',1)){
            return $this->success('已确认联系');
        } else {
            return $this->error('确认失败');
        }
    }
}
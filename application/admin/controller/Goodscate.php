<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/7/31
 * Time: 10:37
 */

namespace app\admin\controller;


use app\common\controller\AdminBase;
use think\Paginator;

class Goodscate extends AdminBase
{

    protected $model;
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\GoodsCate();
    }

    /**
     * 分类列表
     * @return mixed
     * @return
     */
    public function index()
    {
        $data = $this->model->where('status',1)->select();
        $this->assign('cate_list',$data);
        return $this->fetch();
    }

    /**
     * 添加分类
     * @return mixed
     * @return
     */
    public function add()
    {
        return $this->fetch();
    }

    /**
     * 保存信息
     * @return
     */
    public function save()
    {
        if (request()->isPost()){
            $data = request()->post();
             if ($this->model->allowField(true)->save($data)){
                return $this->success('添加成功');
            } else {
                return $this->error('添加失败');
            }

        } else {
            return $this->error('请求方式出错');
        }
    }

    /**
     * 编辑分类
     * @param $id
     * @return mixed
     * @return
     */
    public function edit($id)
    {
        $info = $this->model->where('id',$id)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    /**
     * 保存更新
     * @param $id
     * @return
     */
    public function update($id)
    {
        $data            = $this->request->param();
        if ($this->model->allowField(true)->save($data, $id) !== false) {
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }

    public function delete($id)
    {
        if ($this->model->where('id',$id)->delete()){
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

}
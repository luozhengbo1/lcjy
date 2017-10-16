<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/1
 * Time: 11:03
 */

namespace app\admin\controller;


use app\common\controller\AdminBase;

class Dealer extends AdminBase
{

    protected $model;
    protected  function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Dealer();
    }

    /**
     * 经销商列表
     * @return
     */
    public function index($keyword = '', $page = 1)
    {
        $map = [];
        if ($keyword) {
            $map['name|tel|address'] = ['like', "%{$keyword}%"];
        }
        $dealer_list = $this->model->where($map)->order('id DESC')->paginate(15, false, ['page' => $page]);

        return $this->fetch('index', ['dealer_list' => $dealer_list, 'keyword' => $keyword]);
    }

    /**
     * 添加经销商
     * @return mixed
     * @return
     */
    public function add()
    {
        $data = \app\common\model\Dealer::where('status',1)->field('id,name')->select();
        $this->assign('dealer_list',$data);
        return $this->fetch();
    }

    /**
     * 保存编辑
     * @return
     */
    public function save()
    {
        if (request()->isPost()){

            $data             = request()->post();
            $validate_result  = $this->validate($data,'Dealer');

            if($validate_result !=true){
                return $this->error($validate_result);
            } else {
                if($this->model->allowField(true)->save($data)){
                    return $this->success('添加成功');
                } else {
                    return $this->error('添加失败');
                }
            }
        }
    }

    /**
     * 编辑经销商
     * @param $id
     * @return mixed
     * @return
     */
    public function edit($id)
    {
        $data = \app\common\model\Dealer::where('status',1)->field('id,name')->select();
        $this->assign('dealer_list',$data);

        $info  = $this->model->where('id',$id)->find();
        $this->assign('info',$info);
        return $this->fetch();
    }

    /**
     * 保存修改
     * @param $id
     * @return
     */
    public function update($id)
    {
        if (request()->isPost()){
            $data = request()->param();

            if ($this->model->allowField(true)->save($data,$id)){
                return $this->success('修改成功');
            } else {
                return $this->error('添加失败');
            }
        }
    }

    /**
     * 删除经销商
     * @param $id
     * @return
     */
    public function delete($id)
    {
        if ($this->model->where('id',$id)->delete()){
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    public function info($id)
    {
        $info = $this->model->where('id',$id)->find();
        $this->assign('info',$info);

        $this->getChildUsersOrder($id);
        return $this->fetch();
    }


    protected function getChildUsersOrder($id)
    {
        $model      = new \app\common\model\OrderFenyong();
        $order_list = $model->where(['dealer_id'=>$id])->select();
        //订单列表
        $this->assign('order_list',$order_list);
    }
}
<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/3
 * Time: 14:45
 */

namespace app\admin\controller;


use app\common\controller\AdminBase;
use think\Session;

class Order extends AdminBase
{
    protected  $model;

    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Order();
    }

    /*
     * 订单列表
     */
    public function index($keyword = '', $page = 1)
    {
        $map = [];
        if ($keyword) {
            $map['title'] = ['like', "%{$keyword}%"];
        }
        $order_list = $this->model->where($map)->order('id DESC')->paginate(15, false, ['page' => $page]);

        return $this->fetch('index', ['order_list' => $order_list, 'keyword' => $keyword]);
    }

    /*
     * 创建订单
     */
    public function add()
    {
        $goodsModel = new \app\common\model\Goods();
        $goods_list = $goodsModel->where('status',1)->select();
        $this->assign('goods_list',$goods_list);

        $goods_type_list = \app\common\model\GoodsType::where('status',1)->select();//原浆类型
        $this->assign('goods_type_list',$goods_type_list);
        return $this->fetch();
    }

    public function save()
    {
        if(request()->isPost()){
            $data   = request()->post();
            if($data['amount'] ==0){
                return $this->error('总金额不能为零，请返回重新操作');
            } else {
                $data['author'] = Session::get('admin_id');
                $data['trade_no'] = build_order_no();
                if($this->model->allowField(true)->save($data)){
                    return $this->success('订单生成成功');
                } else {
                    return $this->error('订单生成失败');
                }
            }
        }
    }

    public function getGoodsPrice($id)
    {
        $model = new \app\common\model\GoodsType();
        $info = $model->where('id',$id)->field('price')->find();

        return json($info);
    }

    public function delete($id)
    {
        if($this->model->where('id',$id)->delete()){
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    public function info($id)
    {
        $info = $this->model->where('id',$id)->find();
        $info['address'] = get_address($info['addressid']);
        $this->assign('info',$info);
        return $this->fetch();
    }
    /*
    * 修改类型
    */
    public function edit($id)
    {
        $info = $this->model->where('id',$id)->find();
        $info['address'] = get_address($info['addressid']);
        $this->assign('info',$info);
        return $this->fetch();

    }
    public function editTwo($id)
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
}
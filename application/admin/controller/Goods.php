<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/7/31
 * Time: 09:41
 */

namespace app\admin\controller;


use app\common\controller\AdminBase;

class Goods extends AdminBase
{

    protected $model;
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Goods();
    }

    /**
     * 产品列表
     * @return mixed
     * @return
     */
    public function index($keyword='',$type='',$page=1)
    {
        $map=[];
        if ($keyword) {
            $map['title|id'] = ['like', "%{$keyword}%"];
        }
        if ($type) {
            $map['type'] = $type;
        }
        $goods_list = $this->model->where($map)->order('id DESC')->paginate(10, false, ['page' => $page]);

       $this->assign('goods_list',$goods_list);
       $this->assign('keyword',$keyword);
       $this->assign('cate_list',\app\common\model\GoodsCate::where('status',1)->select());//分类

       return $this->fetch();
    }

    /**
     * 添加产品
     * @return mixed
     * @return
     */
    public function add()
    {
        $cate = \app\common\model\GoodsCate::where('status',1)->select();
        $this->assign('cate_list',$cate);

        $this->type();
        return $this->fetch();
    }

    /**
     * 保存产品
     * @return
     */
    public function save()
    {
        $data = request()->post();
        //遍历价格
        if (!empty($data['marketprice'])){
            for ($i= 0;$i<count($data['marketprice']);$i++){
                $data['sku'][$i]['marketprice'] = $data['marketprice'][$i];
                $data['sku'][$i]['price'] = $data['price'][$i];
            }
        }
        //序列化价格
       if ($this->model->alloWField(true)->save($data)){
            return $this->success('添加成功','/admin/goods/index');
       } else {
           return $this->error('添加失败');
       }
    }

    /**
     * 编辑产品
     * @param $id
     * @return mixed
     * @return
     */
    public function edit($id)
    {
        $info = $this->model->where('id',$id)->find();
        $this->assign('info',$info);
        //分类
        $cate = \app\common\model\GoodsCate::where('status',1)->select();
        $this->assign('cate_list',$cate);
        $this->type();
//        return json($info);
        return $this->fetch();
    }

    public function update($id)
    {
        $data = request()->param();

        if ($this->model->alloWField(true)->save($data,$id)){
            return $this->success('修改成功');
        } else {
            return $this->error('修改失败');
        }
    }
    /**
     * 删除产品
     * @param $id
     * @return
     */
    public function delete($id)
    {
        if ($this->model->where('id',$id)->delete())
        {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    protected function type()
    {
        $goods_type_list = \app\common\model\GoodsType::where('status',1)->field('id,name')->select();
        $this->assign('goods_type_list',$goods_type_list);
    }
}
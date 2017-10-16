<?php
/**
 * Created by PhpStorm.
 * User: ran
 * Date: 2017/9/6
 * Time: 下午3:29
 */

namespace app\admin\controller;


use app\common\controller\AdminBase;
use app\dingtalk\model\FtQrcode;

class Tools extends AdminBase
{

    public function qrcode($page=1)
    {
        $model = new FtQrcode();
        $result = $model->order('id desc')->paginate(10,['page'=>$page]);
        $this->assign('list',$result);
        return $this->fetch('/tools/qcode/index');
    }
}
<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/1
 * Time: 11:07
 */

namespace app\common\model;


use think\Model;

class Dealer extends Model
{

    protected $autoWriteTimestamp = true;

    protected function getUpdateTimeAttr($value)
    {
        return date('y-d-m H:i:s',$value);
    }

    protected function getCreateTimeAttr($value)
    {
        return date('y-d-m H:i:s',$value);
    }
}
<?php
/**
 * $desc
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/8/3
 * Time: 14:46
 */

namespace app\common\model;


use think\Model;

class Order extends Model
{

    protected $autoWriteTimestamp = true;

    /*
     * 转换时间戳
     */
    protected function getCreateTimeAttr($value)
    {
        return date('y-m-d H:i:s',$value);
    }

    /*
     * 转换时间戳
     */
    protected function getUpdateTimeAttr($value)
    {
        return date('y-m-d H:i:s',$value);
    }


    /*
     * 转换时间戳
     */
//    protected function getPaidTimeAttr($value)
//    {
//        if($value ==0){
//            return 0;
//        } else {
//            return date('y-m-d H:i:s',$value);
//        }
//    }
}
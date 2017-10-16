<?php
/**
 * 模板模型
 * Created by PhpStorm.
 * User: torans <321870617@qq.com>
 * Date: 17/7/31
 * Time: 09:51
 */

namespace app\common\model;


use think\Model;

class Goods extends Model
{

    protected $autoWriteTimestamp = true;

    /*
     * 序列化sku
     */
    protected function setSkuAttr($value)
    {
        return serialize($value);
    }

    /*
     * 反序列化sku
     */
    protected function getSkuAttr($value)
    {
        return unserialize($value);
    }

    /*
     * 原浆类型
     */
    protected function setYuanjiangAttr($value)
    {
        return serialize($value);
    }

    protected function getYuanjiangAttr($value)
    {
        return unserialize($value);
    }

    protected function getYjAttr($value)
    {
        $data = unserialize($value);
        for($i = 0;$i<count($data);$i++){
            $data[$i] = get_yuanjiang($data[$i]);
        }

        return $data;
    }

}
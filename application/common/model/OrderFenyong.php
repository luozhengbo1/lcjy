<?php
/**
 * Created by PhpStorm.
 * User: ran
 * Date: 2017/8/31
 * Time: 上午11:08
 */

namespace app\common\model;


use think\Model;

class OrderFenyong extends Model
{

    protected $readonly = ['trade_no','dealer_id','user_id','fenyong','amount'];

}
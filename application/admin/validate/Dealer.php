<?php
namespace app\admin\validate;

use think\Validate;

class Dealer extends Validate
{
    protected $rule = [
        'name' => 'require',
        'tel'  => 'require|number',
        'banknumber'  => 'require|number',
        'address'  => 'require',
        'fenyong'  => 'require|number',
        'cardid'  => 'require',
    ];

    protected $message = [
        'name.require' => '请输入经销商名称',
        'tel.require'  => '请输入联系手机',
        'tel.number'   => '联系电话格式不对',
        'fenyong.require'   => '请输入分佣',
        'fenyong.number'    => '联系电话格式不对',
        'address.require'   => '联系地址必须填写',
        'cardid.require'   => '请输入证件号码',
    ];
}
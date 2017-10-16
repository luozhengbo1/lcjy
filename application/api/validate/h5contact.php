<?php
namespace app\api\validate;

use think\Validate;

/**
 * 友情链接验证器
 * Class Link
 * @package app\admin\validate
 */
class h5contact extends Validate
{
    protected $rule = [
        'name' => 'require',
        'tel'  => 'require'
    ];

    protected $message = [
        'name.require' => '姓名不能为空',
        'tel.require' => '电话不能为空',
        'tel.number' => '电话号码只能是数字'
    ];
}
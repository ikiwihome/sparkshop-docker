<?php


namespace app\admin\validate;

use think\Validate;

class ComImagesCateValidate extends Validate
{
    protected $rule = [
        'pid|上级分类id' => 'require',
        'name|分类名' => 'require',
    ];

    protected $scene = [
        'edit' => ['id', 'name']
    ];
}
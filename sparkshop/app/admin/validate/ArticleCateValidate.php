<?php


namespace app\admin\validate;

use think\Validate;

class ArticleCateValidate extends Validate
{
    protected $rule = [
        'name|分类名' => 'require',
        'status|状态' => 'require',
    ];
}
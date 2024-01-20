<?php


namespace app\admin\validate;

use think\Validate;

class UserGroupValidate extends Validate
{
    protected $rule = [
        'name|分组名称' => 'require',
    ];
}
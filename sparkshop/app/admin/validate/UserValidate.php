<?php


namespace app\admin\validate;

use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'nickname|昵称' => 'require',
        'password|用户密码' => 'require',
        'phone|手机号' => 'require',
        'register_time|注册时间' => 'require',
        'status|状态' => 'require',
    ];

    protected $scene = [
        'edit' => ['nickname', 'phone', 'register_time', 'status']
    ];
}
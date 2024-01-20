<?php


namespace app\admin\validate;

use think\Validate;

class LoginValidate extends Validate
{
    protected $rule = [
        'name|登录名' => 'require',
        'password|密码' => 'require|max:32',
        'code|验证码' => 'require|number'
    ];
}
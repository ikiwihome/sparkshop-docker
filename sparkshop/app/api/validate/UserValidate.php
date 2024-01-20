<?php

namespace app\api\validate;

use think\Validate;

class UserValidate extends Validate
{
    protected $rule = [
        'phone|手机号' => 'require',
        'code|验证码' => 'require',
        'password|密码' => 'require',
        'type|模板' => 'require',
        'open_id|openid' => 'require'
    ];
}
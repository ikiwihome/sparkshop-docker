<?php


namespace app\api\validate;

use think\Validate;

class AddressValidate extends Validate
{
    protected $rule = [
        'phone|手机号' => 'require|mobile',
        'real_name|收件人' => 'require',
        'detail|详细地址' => 'require'
    ];
}
<?php


namespace app\admin\validate;

use think\Validate;

class LabelValidate extends Validate
{
    protected $rule = [
        'name|标签名' => 'require',
    ];
}
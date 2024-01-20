<?php


namespace app\admin\validate;

use think\Validate;

class SetExpressValidate extends Validate
{
    protected $rule = [
        'name|物流公司名称' => 'require',
        'code|物流公司编码' => 'require',
        'status|状态' => 'require',
    ];
}
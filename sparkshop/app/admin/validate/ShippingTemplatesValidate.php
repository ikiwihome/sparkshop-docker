<?php


namespace app\admin\validate;

use think\Validate;

class ShippingTemplatesValidate extends Validate
{
    protected $rule = [
        'name|运费模板名' => 'require',
        'type|计费方式' => 'require',
        'sort|排序值' => 'require',
    ];
}
<?php


namespace app\admin\validate;

use think\Validate;

class GoodsCateValidate extends Validate
{
    protected $rule = [
        'pid|上级分类' => 'require',
        'name|分类名' => 'require',
        'icon|icon图标' => 'require'
    ];
}
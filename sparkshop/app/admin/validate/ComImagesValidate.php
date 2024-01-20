<?php


namespace app\admin\validate;

use think\Validate;

class ComImagesValidate extends Validate
{
    protected $rule = [
        'name|图片名称' => 'require',
        'url|绝对地址' => 'require',
    ];
}
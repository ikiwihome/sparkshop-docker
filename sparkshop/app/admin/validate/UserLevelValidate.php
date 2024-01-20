<?php


namespace app\admin\validate;

use think\Validate;

class UserLevelValidate extends Validate
{
    protected $rule = [
        'title|等级名称' => 'require',
        'level|等级值' => 'require',
        'discount|享受折扣' => 'float|between:1,100',
        'experience|经验值' => 'require',
        'icon|图标' => 'require',
        'status|是否显示' => 'require',
        'remark|等级说明' => 'require',
    ];
}
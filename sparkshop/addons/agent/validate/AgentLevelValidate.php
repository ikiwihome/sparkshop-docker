<?php


namespace addons\agent\validate;

use think\Validate;

class AgentLevelValidate extends Validate
{
    protected $rule = [
        'level|等级值' => 'require|number',
        'name|等级名称' => 'require',
        'percent|分销比例' => 'require|float',
        'level_up_way|升级方式' => 'require|number',
        'config|条件' => 'require'
    ];

    protected $scene = [
        'edit' => ['level', 'name', 'percent', 'level_up_way']
    ];
}
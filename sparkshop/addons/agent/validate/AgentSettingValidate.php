<?php


namespace addons\agent\validate;

use think\Validate;

class AgentSettingValidate extends Validate
{
    protected $rule = [
        'parent_percent|一级返佣比例' => 'require|float',
        'agent_give_back_limit|单日返佣上限' => 'require|number',
        'agent_money_freeze_day|佣金冻结天数' => 'require|number',
        'min_apply_amount|最低提现金额' => 'require|number',
        'apply_way|提现方式' => 'require'
    ];

    protected $scene = [
        'form2' => ['first_level_percent', 'agent_give_back_limit', 'agent_money_freeze_day'],
        'form3' => ['min_apply_amount', 'apply_way'],
    ];
}
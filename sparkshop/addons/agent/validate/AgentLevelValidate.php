<?php
// +----------------------------------------------------------------------
// | SparkShop 坚持做优秀的商城系统
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2099 http://sparkshop.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai  <1902822973@qq.com>
// +----------------------------------------------------------------------

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
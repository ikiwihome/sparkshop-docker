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
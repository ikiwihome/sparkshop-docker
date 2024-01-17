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
namespace addons\seckill\validate;

use think\Validate;

class SeckillTimeValidate extends Validate
{
    protected $rule = [
        'start_hour|开启整点' => 'require|number|between:0,23',
        'continue_hour|持续时长' => 'require|number|between:1,24',
        'status|是否有效' => 'require|number|in:1,2',
        'sort|排序' => 'require|number|between:1,10'
    ];
}
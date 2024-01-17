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

class GoodsCateValidate extends Validate
{
    protected $rule = [
        'pid|上级分类' => 'require',
        'name|分类名' => 'require',
        'icon|icon图标' => 'require'
    ];
}
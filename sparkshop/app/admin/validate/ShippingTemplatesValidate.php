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

class ShippingTemplatesValidate extends Validate
{
    protected $rule = [
        'name|运费模板名' => 'require',
        'type|计费方式' => 'require',
        'sort|排序值' => 'require',
    ];
}
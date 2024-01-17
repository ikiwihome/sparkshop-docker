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

namespace app\api\validate;

use think\Validate;

class AddressValidate extends Validate
{
    protected $rule = [
        'phone|手机号' => 'require|mobile',
        'real_name|收件人' => 'require',
        'detail|详细地址' => 'require'
    ];
}
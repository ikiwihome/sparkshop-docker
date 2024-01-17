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

class UserValidate extends Validate
{
    protected $rule = [
        'phone|手机号' => 'require',
        'code|验证码' => 'require',
        'password|密码' => 'require',
        'type|模板' => 'require',
        'open_id|openid' => 'require'
    ];
}
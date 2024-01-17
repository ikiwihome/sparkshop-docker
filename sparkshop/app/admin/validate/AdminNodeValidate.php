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

class AdminNodeValidate extends Validate
{
    protected $rule = [
        'type|菜单类型' => 'require',
        'name|菜单名' => 'require|max:55',
        'path|权限路由' => 'require|max:55',
        'is_menu|是否菜单' => 'require|number'
    ];
}
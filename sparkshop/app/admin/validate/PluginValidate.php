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

class PluginValidate extends Validate
{
    protected $rule = [
        'name|插件标识' => 'require|max:100',
        'title|插件名称' => 'require|max:155',
        'author|作者名称' => 'require',
        'home_page|插件首页' => 'require',
        'version|版本' => 'require'
    ];
}
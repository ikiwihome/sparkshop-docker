<?php


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
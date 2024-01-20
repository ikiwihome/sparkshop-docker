<?php


namespace app\admin\validate;

use think\Validate;

class AdminRoleValidate extends Validate
{
    protected $rule = [
        'name|角色名称' => 'require',
        'role_node|权限节点' => 'require',
        'status|状态' => 'require',
    ];
}
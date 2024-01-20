<?php


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
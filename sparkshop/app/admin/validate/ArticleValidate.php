<?php


namespace app\admin\validate;

use think\Validate;

class ArticleValidate extends Validate
{
    protected $rule = [
        'title|文章标题' => 'require',
        'cate_id|文章分类id' => 'require',
        'desc|文章描述' => 'require',
        'content|文章内容' => 'require'
    ];
}
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

class ArticleValidate extends Validate
{
    protected $rule = [
        'title|文章标题' => 'require',
        'cate_id|文章分类id' => 'require',
        'desc|文章描述' => 'require',
        'content|文章内容' => 'require'
    ];
}
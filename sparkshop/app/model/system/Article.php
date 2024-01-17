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

namespace app\model\system;

use app\model\BaseModel;

class Article extends BaseModel
{
    public function cateInfo()
    {
        return $this->hasOne(ArticleCate::class, 'id', 'cate_id');
    }
}


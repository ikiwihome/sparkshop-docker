<?php


namespace app\model\system;

use app\model\BaseModel;

class Article extends BaseModel
{
    public function cateInfo()
    {
        return $this->hasOne(ArticleCate::class, 'id', 'cate_id');
    }
}


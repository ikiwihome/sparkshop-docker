<?php


namespace app\model\goods;

use app\model\BaseModel;

class Goods extends BaseModel
{
    public function cate()
    {
        return $this->hasOne(GoodsCate::class, 'id', 'cate_id');
    }
}


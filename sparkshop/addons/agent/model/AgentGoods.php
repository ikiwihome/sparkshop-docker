<?php


namespace addons\agent\model;

use app\model\BaseModel;
use app\model\goods\Goods;

class AgentGoods extends BaseModel
{
    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id')->visible(['id', 'name', 'slider_image', 'sales', 'stock']);
    }
}
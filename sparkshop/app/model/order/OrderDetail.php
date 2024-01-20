<?php


namespace app\model\order;

use app\model\BaseModel;

class OrderDetail extends BaseModel
{
    public function comment()
    {
        return $this->hasOne(OrderComment::class, 'order_detail_id', 'id');
    }
}
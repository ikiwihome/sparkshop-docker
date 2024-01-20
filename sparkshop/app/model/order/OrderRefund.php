<?php

namespace app\model\order;

use app\model\BaseModel;

class OrderRefund extends BaseModel
{
    public function orderInfo()
    {
        return $this->hasOne(Order::class, 'id', 'order_id')->visible(['create_time', 'remark', 'pay_status', 'pay_way']);
    }
}
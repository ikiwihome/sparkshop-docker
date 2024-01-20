<?php


namespace app\model\order;

use app\model\BaseModel;
use app\model\user\User;

class Order extends BaseModel
{
    public function detail()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function address()
    {
        return $this->hasOne(OrderAddress::class, 'order_id', 'id');
    }

    public function refund()
    {
        return $this->hasOne(OrderRefund::class, 'order_id', 'id')->whereIn('status', [1, 2])->visible(['status']);
    }

    public function detailSimple()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id')->visible(['id']);
    }
}
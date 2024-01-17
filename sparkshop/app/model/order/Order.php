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
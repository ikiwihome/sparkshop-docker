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

class OrderRefund extends BaseModel
{
    public function orderInfo()
    {
        return $this->hasOne(Order::class, 'id', 'order_id')->visible(['create_time', 'remark', 'pay_status', 'pay_way']);
    }
}
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
namespace addons\coupon\model;

use app\model\BaseModel;

class CouponReceiveLog extends BaseModel
{
    public function coupon()
    {
        return $this->hasOne(Coupon::class, 'id', 'coupon_id');
    }
}
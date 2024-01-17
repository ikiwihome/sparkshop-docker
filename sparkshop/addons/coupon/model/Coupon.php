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

class Coupon extends BaseModel
{
    public function goods()
    {
        return $this->hasMany(CouponGoods::class, 'coupon_id', 'id');
    }

    public function couponGoods()
    {
        return $this->hasMany(CouponGoods::class, 'coupon_id', 'id')->visible(['id']);
    }
}
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

namespace addons\coupon\controller\api;

use addons\coupon\service\CouponService;
use app\BaseController;

class Index extends BaseController
{
    /**
     * 获取可领取的优惠券
     */
    public function getCouponList()
    {
        $param = input('param.');

        $couponService = new CouponService();
        return json($couponService->getCanReceiveList($param));
    }
}
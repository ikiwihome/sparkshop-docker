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

namespace addons\coupon\controller\index;

use addons\coupon\service\CouponReceiveService;
use addons\coupon\service\CouponService;
use app\BaseController;

class Index extends BaseController
{
    /**
     * 获取可用的优惠券
     */
    public function getMyValid()
    {
        $param = input('post.');

        $couponReceiveService = new CouponReceiveService();
        return json($couponReceiveService->getMyCoupon($param, 2));
    }

    /**
     * 领取优惠券
     */
    public function receive()
    {
        $param = input('post.');

        $couponReceiveService = new CouponReceiveService();
        return json($couponReceiveService->userReceive($param, 2));
    }

    /**
     * 获取我的优惠券
     */
    public function myCoupon()
    {
        $param = input('param.');

        $couponService = new CouponService();
        return json($couponService->getMyCouponList($param, 2));
    }

    /**
     * 获取可领取的优惠券
     */
    public function getCouponList()
    {
        $param = input('param.');

        $couponService = new CouponService();
        return json($couponService->getCanReceiveList($param, 2));
    }
}
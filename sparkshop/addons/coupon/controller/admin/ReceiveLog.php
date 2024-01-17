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

namespace addons\coupon\controller\admin;

use addons\coupon\service\CouponReceiveService;
use app\PluginBaseController;

class ReceiveLog extends PluginBaseController
{
    /**
     * 使用列表
     */
    public function index()
    {
        if (request()->isAjax()) {

            $couponService = new CouponReceiveService();
            $res = $couponService->getCouponReceiveLog(input('param.'));
            return json($res);
        }

        return fetch();
    }
}
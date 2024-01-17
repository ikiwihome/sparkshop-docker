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
namespace app\api\controller;

use app\service\BalanceRechargeService;

class Balance extends Base
{
    /**
     * 余额充值
     */
    public function recharge(BalanceRechargeService $balanceRechargeService)
    {
        $param = input('post.');
        $param['platform'] = isset(request()->header()['x-csrf-token']) ? request()->header()['x-csrf-token'] : '';

        return json($balanceRechargeService->createOrder($param));
    }
}
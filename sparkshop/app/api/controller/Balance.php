<?php

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
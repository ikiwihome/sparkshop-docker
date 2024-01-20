<?php


namespace app\index\controller;

use app\service\BalanceRechargeService;

class Balance extends Base
{
    public function initialize()
    {
        parent::initialize();
        pcLoginCheck();
    }

    /**
     * 余额充值
     */
    public function recharge(BalanceRechargeService $balanceRechargeService)
    {
        $param = input('post.');
        $param['platform'] = '';

        return json($balanceRechargeService->createOrder($param, 2));
    }

    /**
     * 检测订单状态
     */
    public function checkOrderStatus(BalanceRechargeService $balanceRechargeService)
    {
        return json($balanceRechargeService->checkOrderStatus(input('param.order')));
    }
}
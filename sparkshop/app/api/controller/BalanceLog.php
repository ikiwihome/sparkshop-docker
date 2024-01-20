<?php

namespace app\api\controller;

use app\service\BalanceLogService;

class BalanceLog extends Base
{
    /**
     * 余额记录
     */
    public function index(BalanceLogService $balanceLogService)
    {
        $param = input('param.');
        $param['user_id'] = $this->user['id'];

        return json($balanceLogService->getBalanceList($param));
    }

    /**
     * 获取基础信息
     */
    public function getTotalInfo(BalanceLogService $balanceLogService)
    {
        $param = input('param.');
        $param['user_id'] = $this->user['id'];

        return json($balanceLogService->getTotalInfo($param));
    }
}
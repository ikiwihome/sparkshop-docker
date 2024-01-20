<?php

namespace app\index\controller;

use app\service\BalanceLogService;

class BalanceLog extends Base
{
    public function initialize()
    {
        parent::initialize();
        pcLoginCheck();
    }

    /**
     * 余额记录
     */
    public function index(BalanceLogService $balanceLogService)
    {
        $param = input('param.');
        $userInfo = getFrontUserInfo();
        $param['user_id'] = $userInfo['id'];

        return json($balanceLogService->getBalanceList($param));
    }

    /**
     * 获取基础信息
     */
    public function getTotalInfo(BalanceLogService $balanceLogService)
    {
        $param = input('param.');
        $userInfo = getFrontUserInfo();
        $param['user_id'] = $userInfo['id'];

        return json($balanceLogService->getTotalInfo($param));
    }
}
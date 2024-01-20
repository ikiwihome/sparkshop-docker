<?php


namespace app\admin\controller;

use app\admin\service\FinanceService;
use think\facade\View;

class FinanceLog extends Base
{
    /**
     * 余额充值记录
     */
    public function index(FinanceService $financeService)
    {
        if (request()->isAjax()) {

            return json($financeService->rechargeLog(input('param.')));
        }

        return View::fetch();
    }
}
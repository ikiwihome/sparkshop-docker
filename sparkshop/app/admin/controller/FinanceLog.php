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
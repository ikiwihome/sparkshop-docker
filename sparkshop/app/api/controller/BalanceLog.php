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
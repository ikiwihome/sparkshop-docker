<?php


namespace app\admin\service;

use app\model\user\UserBalanceRecharge;

class FinanceService
{
    /**
     * 充值记录
     * @param array $param
     * @return array
     */
    public function rechargeLog(array $param) : array
    {
        $where = [];
        if (!empty($param['status'])) {
            $where[] = ['status', '=', $param['status']];
        }

        if (!empty($param['user_id'])) {
            $where[] = ['user_id', '=', $param['user_id']];
        }

        if (!empty($param['date'])) {
            $where[] = ['create_time', '>=', $param['date'] . ' 00:00:00'];
            $where[] = ['create_time', '<=', $param['date'] . ' 23:59:59'];
        }

        $list = (new UserBalanceRecharge())->getPageList($param['limit'], $where)['data'];

        return dataReturn(0, 'success', $list);
    }
}
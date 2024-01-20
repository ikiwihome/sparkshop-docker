<?php

namespace app\admin\service;

use app\model\order\Order;
use app\model\system\AdminUser;
use app\model\user\User;
use app\model\user\UserPv;

class HomeService
{
    /**
     * 销售金额数据
     * @return array
     */
    public function sellAmountData(): array
    {
        try {

            // 今日销售额
            $orderModel = new Order();
            $day = date('Y-m-d');
            $yesterdayData = $orderModel->where('pay_status', 2)->whereBetween('create_time', [
                $day, $day . ' 23:59:59'
            ])->sum('pay_price');
            $yesterdayData = round($yesterdayData, 2);

            // 日销售环比
            $beforeYesterday = date('Y-m-d', strtotime('-1 day'));
            $beforeYesterdayData = $orderModel->where('pay_status', 2)->whereBetween('create_time', [
                $beforeYesterday, $beforeYesterday . ' 23:59:59'
            ])->sum('pay_price'); // 前天销售额
            if ($yesterdayData == 0) {
                $dayRatio = '--';
            } else {
                $dayRatio = round((($yesterdayData - $beforeYesterdayData) / $yesterdayData) * 100, 2);
            }

            // 周环比销售
            $weekDate = date('Y-m-d', strtotime('-7 day'));
            $weekData = $orderModel->where('pay_status', 2)->whereBetween('create_time', [
                $weekDate, $day . ' 23:59:59'
            ])->sum('pay_price'); // 上周销售额

            $preWeekDateStart = date('Y-m-d', strtotime('-14 day'));
            $preWeekDateEnd = date('Y-m-d', strtotime('-8 day'));
            $preWeekData = $orderModel->where('pay_status', 2)->whereBetween('create_time', [
                $preWeekDateStart, $preWeekDateEnd . ' 23:59:59'
            ])->sum('pay_price'); // 上上周销售额
            if ($weekData == 0) {
                $weekRatio = '--';
            } else {
                $weekRatio = round((($weekData - $preWeekData) / $weekData) * 100, 2);
            }

            // 本月销售额
            $monthData = $orderModel->where('pay_status', 2)->where('create_time', '>', date('Y-m-01') . ' 00:00:00')->sum('pay_price');
            $monthData = round($monthData, 2);
            $data = compact('yesterdayData', 'dayRatio', 'weekRatio', 'monthData');

        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, 'success', $data);
    }

    /**
     * 用户pv数据
     * @return array
     */
    public function userPVData(): array
    {
        try {

            // 今日访问数据
            $userPVModel = new UserPv();
            $day = date('Y-m-d');
            $yesterdayData = $userPVModel->whereBetween('create_time', [
                $day, $day . ' 23:59:59'
            ])->count('id');
            $yesterdayData = round($yesterdayData, 2);

            // 日访问数据
            $beforeYesterday = date('Y-m-d', strtotime('-1 day'));
            $beforeYesterdayData = $userPVModel->whereBetween('create_time', [
                $beforeYesterday, $beforeYesterday . ' 23:59:59'
            ])->count('id'); // 前天访问量
            if ($yesterdayData == 0) {
                $dayRatio = '--';
            } else {
                $dayRatio = round((($yesterdayData - $beforeYesterdayData) / $yesterdayData) * 100, 2);
            }

            // 周环比访问
            $weekDate = date('Y-m-d', strtotime('-7 day'));
            $weekData = $userPVModel->whereBetween('create_time', [
                $weekDate, $day . ' 23:59:59'
            ])->count('id'); // 上周访问量

            $preWeekDateStart = date('Y-m-d', strtotime('-14 day'));
            $preWeekDateEnd = date('Y-m-d', strtotime('-8 day'));
            $preWeekData = $userPVModel->whereBetween('create_time', [
                $preWeekDateStart, $preWeekDateEnd . ' 23:59:59'
            ])->count('id'); // 上上周访问量
            if ($weekData == 0) {
                $weekRatio = '--';
            } else {
                $weekRatio = round((($weekData - $preWeekData) / $weekData) * 100, 2);
            }

            // 本月访问
            $monthData = $userPVModel->where('create_time', '>', date('Y-m-01') . ' 00:00:00')->count('id');
            $monthData = round($monthData, 2);
            $data = compact('yesterdayData', 'dayRatio', 'weekRatio', 'monthData');

        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, 'success', $data);
    }

    /**
     * 订单数据
     * @return array
     */
    public function orderData(): array
    {
        try {

            // 今日订单量
            $orderModel = new Order();
            $day = date('Y-m-d');
            $yesterdayData = $orderModel->where('pay_status', 2)->whereBetween('create_time', [
                $day, $day . ' 23:59:59'
            ])->count('id');
            $yesterdayData = round($yesterdayData, 2);

            // 日订单量
            $beforeYesterday = date('Y-m-d', strtotime('-1 day'));
            $beforeYesterdayData = $orderModel->where('pay_status', 2)->whereBetween('create_time', [
                $beforeYesterday, $beforeYesterday . ' 23:59:59'
            ])->count('id'); // 前天订单量
            if ($yesterdayData == 0) {
                $dayRatio = '--';
            } else {
                $dayRatio = round((($yesterdayData - $beforeYesterdayData) / $yesterdayData) * 100, 2);
            }

            // 周环比订单量
            $weekDate = date('Y-m-d', strtotime('-7 day'));
            $weekData = $orderModel->where('pay_status', 2)->whereBetween('create_time', [
                $weekDate, $day . ' 23:59:59'
            ])->count('id'); // 上周订单量

            $preWeekDateStart = date('Y-m-d', strtotime('-14 day'));
            $preWeekDateEnd = date('Y-m-d', strtotime('-8 day'));
            $preWeekData = $orderModel->where('pay_status', 2)->whereBetween('create_time', [
                $preWeekDateStart, $preWeekDateEnd . ' 23:59:59'
            ])->count('id'); // 上上周订单量
            if ($weekData == 0) {
                $weekRatio = '--';
            } else {
                $weekRatio = round((($weekData - $preWeekData) / $weekData) * 100, 2);
            }

            // 本月订单量
            $monthData = $orderModel->where('pay_status', 2)->where('create_time', '>', date('Y-m-01') . ' 00:00:00')->count('id');
            $monthData = round($monthData, 2);
            $data = compact('yesterdayData', 'dayRatio', 'weekRatio', 'monthData');

        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, 'success', $data);
    }

    /**
     * 用户注册数据
     * @return array
     */
    public function userData(): array
    {
        try {

            // 昨日注册量
            $userModel = new User();
            $day = date('Y-m-d');
            $yesterdayData = $userModel->whereBetween('register_time', [
                $day, $day . ' 23:59:59'
            ])->count('id');
            $yesterdayData = round($yesterdayData, 2);

            // 日注册量
            $beforeYesterday = date('Y-m-d', strtotime('-1 day'));
            $beforeYesterdayData = $userModel->whereBetween('register_time', [
                $beforeYesterday, $beforeYesterday . ' 23:59:59'
            ])->count('id'); // 前天注册量
            if ($yesterdayData == 0) {
                $dayRatio = '--';
            } else {
                $dayRatio = round((($yesterdayData - $beforeYesterdayData) / $yesterdayData) * 100, 2);
            }

            // 周环比注册量
            $weekDate = date('Y-m-d', strtotime('-7 day'));
            $weekData = $userModel->whereBetween('register_time', [
                $weekDate, $day . ' 23:59:59'
            ])->count('id'); // 上周注册量

            $preWeekDateStart = date('Y-m-d', strtotime('-14 day'));
            $preWeekDateEnd = date('Y-m-d', strtotime('-8 day'));
            $preWeekData = $userModel->whereBetween('register_time', [
                $preWeekDateStart, $preWeekDateEnd . ' 23:59:59'
            ])->count('id'); // 上上周注册量
            if ($weekData == 0) {
                $weekRatio = '--';
            } else {
                $weekRatio = round((($weekData - $preWeekData) / $weekData) * 100, 2);
            }

            // 本月注册量
            $monthData = $userModel->where('register_time', '>', date('Y-m-01') . ' 00:00:00')->count('id');
            $monthData = round($monthData, 2);
            $data = compact('yesterdayData', 'dayRatio', 'weekRatio', 'monthData');

        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, 'success', $data);
    }

    /**
     * 最近15日订单数据
     * @return array
     */
    public function fifteenDayOrderData(): array
    {
        $start = date('Y-m-d', strtotime('-15 days'));
        $orderNumData = [];
        $orderAmountData = [];
        $timeLine = [];

        for ($i = 15; $i > 0; $i--) {
            $day = date('Y-m-d', strtotime('-' . $i . ' days'));
            $orderNumData[$day] = 0;
            $orderAmountData[$day] = 0;
            $timeLine[] = $day;
        }

        $orderModel = new Order();
        $res = $orderModel->field('DATE_FORMAT(create_time, "%Y-%m-%d") as `day`,count(*) as t_total,sum(pay_price) as amount')
            ->where('pay_status', 2)
            ->where('create_time', '>', $start)
            ->where('create_time', '<', date('Y-m-d') . ' 23:59:59')
            ->group('DATE_FORMAT(create_time, "%Y-%m-%d")')
            ->select();

        if (!empty($res)) {
            foreach ($res as $vo) {
                if (isset($orderNumData[$vo['day']])) {
                    $orderNumData[$vo['day']] = $vo['t_total'];
                    $orderAmountData[$vo['day']] = $vo['amount'];
                }
            }
        }

        return dataReturn(0, 'success', [
           'timeLine' =>  $timeLine,
            'numData' => array_values($orderNumData),
            'amountData' => array_values($orderAmountData),
        ]);
    }

    /**
     * 最近15日注册
     */
    public function registerData(): array
    {
        $start = date('Y-m-d', strtotime('-15 days'));
        $regNumData = [];
        $timeLine = [];

        for ($i = 15; $i > 0; $i--) {
            $day = date('Y-m-d', strtotime('-' . $i . ' days'));
            $regNumData[$day] = 0;
            $timeLine[] = $day;
        }

        $userModel = new User();
        $res = $userModel->field('DATE_FORMAT(register_time, "%Y-%m-%d") as `day`,count(*) as t_total')
            ->where('register_time', '>', $start)
            ->where('register_time', '<', date('Y-m-d') . ' 23:59:59')
            ->group('DATE_FORMAT(register_time, "%Y-%m-%d")')
            ->select();

        if (!empty($res)) {
            foreach ($res as $vo) {
                if (isset($regNumData[$vo['day']])) {
                    $regNumData[$vo['day']] = $vo['t_total'];
                }
            }
        }

        return dataReturn(0, 'success', [
            'timeLine' =>  $timeLine,
            'numData' => array_values($regNumData),
        ]);
    }

    /**
     * 修改密码
     * @param array $param
     * @return array
     */
    public function editPassword(array $param) : array
    {
        if (empty($param['old_pwd']) || empty($param['new_pwd'])) {
            return dataReturn(-3, '旧密码或新密码不能为空');
        }

        $adminModel = new AdminUser();

        $info = $adminModel->findOne(['id' => session('admin_id')])['data'];
        if (empty($info)) {
            return dataReturn(-1, '管理员信息不存在');
        }

        if ($info['password'] != makePassword($param['old_pwd'], $info['salt'])) {
            return dataReturn(-2, '旧密码错误');
        }

        $salt = uniqid();
        $adminModel->updateById([
            'password' => makePassword($param['new_pwd'], $salt),
            'salt' => $salt
        ], $info['id']);

        return dataReturn(0, '修改成功');
    }
}
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

namespace addons\agent\subscribe;

use addons\agent\model\AgentGoods;
use addons\agent\model\AgentOrder;
use addons\agent\model\AgentRebateFrozen;
use addons\agent\model\AgentUser;
use addons\agent\service\AgentLevelUp;
use addons\agent\service\AgentOrderService;
use addons\agent\service\AgentUserService;
use app\model\order\Order;
use app\model\order\OrderDetail;
use app\model\user\User;

class AgentSubscribe
{
    /**
     * 绑定推广关系
     * @param $param
     * @return array
     */
    public function onBindAgentUser($param) : array
    {
        $config = getConfByType('agent');
        if ($config['agent_open'] != 1) {
            return dataReturn(-5, '分销已关闭');
        }

        if (empty($param['reg_param']['spId'])) {
            return dataReturn(-6, '分销员数据有误');
        }

        $userModel = new User();
        $agentUserModel = new AgentUser();
        $userId = $param['user_param']['id'];

        // 分销模式 人人分销
        if ($config['agent_type'] == 2) {
            $agentUserService = new AgentUserService();

            // 分销的人变为分销员
            $has = $agentUserModel->findOne([
                'user_id' => $param['reg_param']['spId']
            ], 'id')['data'];
            if (empty($has)) {
                $agentUserService->initUser($param['reg_param']['spId']);
            }
            $userModel->updateById([
                'is_agent' => 1
            ], $param['reg_param']['spId']);

            // 新用户变为分销员
            $has = $agentUserModel->findOne([
                'user_id' => $userId
            ], 'id')['data'];
            if (empty($has)) {
                $agentUserService->initUser($userId);
            }
            // 绑定关系
            $agentUserModel->updateByWhere([
                'parent_spread_id' => $param['reg_param']['spId']
            ], ['user_id' => $userId]);

            $userModel->updateById([
                'is_agent' => 1
            ], $userId);
        }

        if (!empty($param['reg_param']['spId'])) {
            // 绑定关系
            $userModel->updateById([
                'parent_spread_id' => $param['reg_param']['spId']
            ], $userId);

            // 增加一个推广人员，给推广人推广金额
            $agentUserModel->where('user_id', $param['reg_param']['spId'])
                ->inc('total_bonus', $config['agent_per_person'])
                ->inc('residue_amount', $config['agent_per_person'])
                ->inc('spread_num', 1)->update();

            // 判断推广人是否可以升级
            return (new AgentLevelUp())->checkLevelUp($param['reg_param']['spId']);
        }

        return dataReturn(0, 'success');
    }

    /**
     * 购买后写入分销数据
     * @param $param
     * @return array
     */
    public function onAddAgentData($param) : array
    {
        $config = getConfByType('agent');
        if ($config['agent_open'] != 1) {
            return dataReturn(-10, '分销关闭');
        }

        $agentUserService = new AgentUserService();
        $agentOrderService = new AgentOrderService();

        // 检测自己是否要升级
        $agentUserModel = new AgentUser();
        $agentUserInfo = $agentUserModel->findOne(['user_id' => $param['user_id']])['data'];

        // 不存在尝试添加为分销员
        if (empty($agentUserInfo)) {

            $goodsDetailList = (new OrderDetail())->getAllList([
                'order_id' => $param['id']
            ], 'goods_id')['data'];

            $goodsIds = [];
            foreach ($goodsDetailList as $vo) {
                $goodsIds[] = $vo['goods_id'];
            }

            $selfBecomeAgent = $agentUserService->checkAndAddUser([
                'user_id' => $param['user_id'],
                'goods_id' => $goodsIds
            ], $config)['data'];

            // 开启自购分佣金
            if ($config['agent_self_buy'] == 1 && $selfBecomeAgent) {
                $agentUserModel->updateByWhere([
                    'self_buy_num' => 1,
                    'self_buy_amount' => $param['order_price'],
                    'self_buy_pay_amount' => $param['pay_price']
                ], ['user_id' => $param['user_id']]);

                // 记录提成订单
                $agentOrderService->addAgentOrder($param, $param['user_id'], $config, 1);
            }
        }

        // 如果已经是分销员
        if (!empty($agentUserInfo)) {

            // 开启自购分佣金
            if ($config['agent_self_buy'] == 1) {
                $agentUserModel->updateByWhere([
                    'self_buy_num' => $agentUserInfo['self_buy_num'] + 1,
                    'self_buy_amount' => $agentUserInfo['self_buy_amount'] + $param['order_price'],
                    'self_buy_pay_amount' => $agentUserInfo['self_buy_pay_amount'] + $param['pay_price']
                ], ['user_id' => $param['user_id']]);

                // 记录提成订单
                $agentOrderService->addAgentOrder($param, $param['user_id'], $config, $agentUserInfo['level_id']);
            }

            // 如果有上级，则给上级计算
            if (!empty($agentUserInfo['parent_spread_id'])) {
                $agentUserModel->updateByWhere([
                    'agent_order_num' => $agentUserInfo['agent_order_num'] + 1,
                    'agent_order_amount' => $agentUserInfo['agent_order_amount'] + $param['order_price'],
                    'agent_pay_amount' => $agentUserInfo['agent_pay_amount'] + $param['pay_price']
                ], ['user_id' => $agentUserInfo['parent_spread_id']]);

                // 记录提成订单
                $agentOrderService->addAgentOrder($param, $agentUserInfo['parent_spread_id'], $config, $agentUserInfo['level_id'], 2);
            }
        }

        return dataReturn(0);
    }

    /**
     * 分销结算
     * @param $param
     * @return array
     */
    public function onAgentSettlement($param) : array
    {
        $config = getConfByType('agent');
        if ($config['agent_open'] != 1) {
            return dataReturn(-5, '分销已关闭');
        }

        $agentOrder = new AgentOrder();
        $readyToFrozen = $agentOrder->getAllList([
            'order_id' => $param['id'],
            'status' => 1
        ])['data']->toArray();

        if (empty($readyToFrozen)) {
           return dataReturn(-1, '该订单不是分销订单');
        }

        $frozenOrder = [];
        $ids = [];
        foreach ($readyToFrozen as $vo) {
            $ids[] = $vo['id'];

            $frozenOrder[] = [
                'order_id' => $vo['order_id'],
                'overdue_time' => date('Y-m-d H:i:s', time() + $config['agent_money_freeze_day'] * 86400),
                'agent_order_id' => $vo['id'],
                'user_id' => $vo['agent_user_id'],
                'spread_amount' => $vo['spread_amount'],
                'create_time' => now()
            ];
        }

        // 记录冻结佣金
        (new AgentRebateFrozen())->insertBatch($frozenOrder);

        // 标记冻结中
        $agentOrder->updateByIds([
            'status' => 2
        ], $ids);

        return dataReturn(0);
    }

    /**
     * 佣金自动结算
     * @return void
     */
    public function onAutoSettlement() : void
    {
        $orderModel =  new AgentRebateFrozen();
        $agentOrderModel =  new AgentOrder();
        $agentUserModel = new AgentUser();
        $agentLevelUp = new AgentLevelUp();

        $orderNum = $orderModel->where('overdue_time', '<', now())->count('id');
        echo "符合结算条件的订单: " . $orderNum . "条" . PHP_EOL;
        if ($orderNum > 0) {

            $limit = 1000;
            $page = 1;
            $pageSize = ceil($orderNum / $limit);

            for ($i = 0; $i < $pageSize; $i++) {
                $ids = [];
                $agentOrderIds = [];
                $offset = ($page - 1) * $limit;

                $orderList = $orderModel->where('overdue_time', '<', now())->limit($offset, $limit)->select()->toArray();
                foreach ($orderList as $vo) {

                    $ids[] = $vo['id'];
                    $agentOrderIds[] = $vo['agent_order_id'];

                    // 结算佣金
                    $agentUserModel->where('user_id', $vo['user_id'])
                        ->inc('residue_amount', $vo['spread_amount'])
                        ->inc('total_bonus', $vo['spread_amount'])->update();

                    // 检测是否可以升级
                    $agentLevelUp->checkLevelUp($vo['user_id']);
                }

                $agentOrderModel->updateByIds([
                    'status' => 4 // 已结算
                ], $agentOrderIds);

                $orderModel->delByIds($ids);

                $page += 1;
            }
        }
    }

    /**
     * 佣金退款
     * @return array
     */
    public function onAgentCancel($param) : array
    {
        $orderModel =  new AgentRebateFrozen();
        $agentOrderModel =  new AgentOrder();
        $agentUserModel = new AgentUser();

        // 退款人推广信息
        $agentUserInfo = $agentUserModel->findOne(['user_id' => $param['user_id']])['data'];

        // 退款订单信息
        $orderInfo = $agentOrderModel->findOne(['buy_user_id' => $param['user_id'], 'order_id' => $param['order_id']])['data'];
        if (empty($orderInfo)) {
            return dataReturn(-2, '该订单非分销订单');
        }

        // 如果整单都退款
        if ($param['order_close']) {

            $orderNum = 1;
            $orderAmount = $orderInfo['order_amount'];
            $payAmount = $orderInfo['pay_amount'];

            $orderModel->delByWhere(['order_id' => $param['order_id']]);
            $agentOrderModel->updateByWhere([
                'status' => 3 // 已退款
            ], ['order_id' => $param['order_id']]);
        } else {

            // 退款的订单数据
            $orderList = (new Order())->getAllList([
                'pid' => $param['order_id'],
                'pay_status' => 3,
                'status' => 8
            ])['data'];

            $orderNum = 0;
            $orderAmount = 0;
            $payAmount = 0;
            foreach ($orderList as $vo) {
                $orderNum += $vo['total_num'];
                $orderAmount += $vo['order_price'];
                $payAmount += $vo['pay_price'];
            }
        }

        // 减少自己金额等数据
        if (!empty($agentUserInfo)) {
            $agentUserModel->where('user_id', $param['user_id'])
                ->dec('self_buy_num', $orderNum)
                ->dec('self_buy_amount', $orderAmount)
                ->dec('self_buy_pay_amount', $payAmount)
                ->update();
        }

        $agentParentId = 0;
        if (empty($agentUserInfo['parent_spread_id'])) {
            $userInfo = (new User())->findOne(['id' => $param['user_id']], 'parent_spread_id')['data'];
            if (!empty($userInfo)) {
                $agentParentId = $userInfo['parent_spread_id'];
            }
        } else {
            $agentParentId = $agentUserInfo['parent_spread_id'];
        }

        // 如果有上级，则给上级计算
        if (!empty($agentParentId)) {
            $agentUserModel->where('user_id', $agentParentId)
                ->dec('agent_order_num', $orderNum)
                ->dec('agent_order_amount', $orderAmount)
                ->dec('agent_pay_amount', $payAmount)
                ->update();
        }

        return dataReturn(0);
    }

    /**
     * 检测是否可以指定分销员
     * @return array
     */
    public function onCheckAgentAppoint() : array
    {
        $config = getConfByType('agent');

        $canAgent = $config['agent_type'] == 1 || $config['agent_type'] == 2;
        return dataReturn(0, 'success', $canAgent);
    }

    /**
     * 检测当前用户是否可以分销
     * @param $param
     * @return array
     */
    public function onCheckNowUserCanShare($param) : array
    {
        $config = getConfByType('agent');

        if ($config['agent_open'] == 2) {
            return dataReturn(0, 'success', false);
        }

        // 指定分销,当前人未被指定，则不可分销
        if ($config['agent_type'] == 1) {
            $agentUserInfo = [];
            if (isset($param['id'])) {
                $agentUserInfo = (new AgentUser())->findOne(['user_id' => $param['id']])['data'];
            }

            if (empty($agentUserInfo)) {
                return dataReturn(0, 'success', false);
            }
        }

        // 当前商品是否可以分销
        $has = (new AgentGoods())->findOne(['goods_id' => $param['goods_id']])['data'];
        if (empty($has)) {
            return dataReturn(0, 'success', false);
        }

        return dataReturn(0, 'success', true);
    }
}
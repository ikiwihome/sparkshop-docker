<?php


namespace addons\agent\service;

use addons\agent\model\AgentLevel;
use addons\agent\model\AgentOrder;

class AgentOrderService
{
    /**
     * 写入分销订单
     * @param $orderInfo
     * @param int $spreadId
     * @param array $config
     * @param int $levelId
     * @param int $type 1:自购分佣 2:上级分佣
     * @return array
     */
    public function addAgentOrder($orderInfo, int $spreadId, array $config, int $levelId, int $type = 1) : array
    {
        // 基础分佣比例
        $basePercent = $config['parent_percent'];
        if ($type == 1) {
            $basePercent = $config['self_buy_percent'];
        }

        // 等级上浮比例
        $upPercent = (new AgentLevel())->findOne(['id' => $levelId])['data']->percent;

        // 提成金额计算 金额100 基础分佣比例 0.1 上浮 0.1 则最终佣金为 100 * 0.1 = 10 , 10 + 10 * 0.1 = 11
        if ($config['agent_give_back'] == 1) { // 按商品价格
            $baseAmount = $orderInfo['order_price'] * $basePercent;
            $spreadAmount = round($baseAmount + $baseAmount * $upPercent, 2);
        } else { // 按实际支付
            $baseAmount = $orderInfo['pay_price'] * $basePercent;
            $spreadAmount = round($baseAmount + $baseAmount * $upPercent, 2);
        }

        $orderParam = [
            'order_id' => $orderInfo['id'],
            'order_no' => $orderInfo['order_no'],
            'buy_user_id' => $orderInfo['user_id'],
            'agent_user_id' => $spreadId,
            'order_amount' => $orderInfo['order_price'],
            'pay_amount' => $orderInfo['pay_price'],
            'agent_percent' => json_encode(['base' => $basePercent, 'up' => $upPercent]),
            'spread_amount' => $spreadAmount,
            'status' => 1,
            'create_time' => now()
        ];

        return (new AgentOrder())->insertOne($orderParam);
    }
}
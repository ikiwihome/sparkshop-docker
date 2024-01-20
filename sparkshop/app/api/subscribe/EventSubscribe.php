<?php


namespace app\api\subscribe;

class EventSubscribe
{
    /**
     * 注册后事件处理
     * @param $param
     * @return array
     */
    public function onAfterReg($param): array
    {
        if (hasInstalled('agent')) {
            // 绑定分销关系
            event('BindAgentUser', $param);
        }

        return dataReturn(0);
    }

    /**
     * 支付回调后事件处理
     * @param $param
     * @return array
     */
    public function onAfterPay($param): array
    {
        if (hasInstalled('agent')) {
            // 触发写入分销数据
            event('AddAgentData', $param);
        }

        return dataReturn(0);
    }

    /**
     * 确认收货后事件处理
     * @param $param
     * @return array
     */
    public function onAfterReceiptOrder($param) : array
    {
        if (hasInstalled('agent')) {
            // 触发分销结算
            event('AgentSettlement', $param);
        }

        return dataReturn(0);
    }

    /**
     * 退款成功后事件处理
     * @param $param
     * @return array
     */
    public function onAfterCancel($param) : array
    {
        if (hasInstalled('agent')) {
            // 触发佣金退款
            event('AgentCancel', $param);
        }

        return dataReturn(0);
    }
}
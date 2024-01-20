<?php


namespace addons\agent\controller\admin;

use addons\agent\service\AgentService;
use addons\agent\service\AgentWithdrawalService;
use app\PluginBaseController;

class Agent extends PluginBaseController
{
    /**
     * 保存设置
     */
    public function setting()
    {
        if (request()->isPost()) {

            $param = input('post.');

            $agentService = new AgentService();
            return json($agentService->saveSetting($param));
        }

        if (request()->isAjax()) {

            $agentInfo = getConfByType('agent');
            return jsonReturn(0, 'SUCCESS', $agentInfo);
        }

        return fetch();
    }

    /**
     * 提现申请
     */
    public function applyLog()
    {
        $agentWithdrawalService = new AgentWithdrawalService();
        if (request()->isPost()) {

            $param = input('post.');

            return json($agentWithdrawalService->dealWithdrawal($param));
        }

        if (request()->isAjax()) {

            $param = input('param.');

            return json($agentWithdrawalService->getAdminWithdrawalLog($param));
        }

        return fetch();
    }
}
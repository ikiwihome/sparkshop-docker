<?php


namespace addons\agent\controller\admin;

use addons\agent\service\AgentLevelService;
use addons\agent\service\AgentUserService;
use app\PluginBaseController;

class AgentUser extends PluginBaseController
{
    /**
     * 分销用户列表
     */
    public function index()
    {
        if (request()->isAjax()) {

            $param = input('param.');

            $agentUserService = new AgentUserService();
            return json($agentUserService->getAgentUserList($param));
        }

        return fetch();
    }

    /**
     * 推广人
     */
    public function detail()
    {
        if (request()->isAjax()) {

            $param = input('param.');

            $agentUserService = new AgentUserService();
            return json($agentUserService->getLowerAgentUser($param));
        }
    }

    /**
     * 推广订单
     */
    public function order()
    {
        if (request()->isAjax()) {

            $param = input('param.');

            $agentUserService = new AgentUserService();
            return json($agentUserService->getAgentOrderList($param));
        }
    }

    /**
     * 取消推销员
     */
    public function del()
    {
        $userId = input('param.user_id');

        $agentUserService = new AgentUserService();
        return json($agentUserService->cancelAgentUser($userId));
    }

    /**
     * 修改分销等级
     */
    public function level()
    {
        if (request()->isPost()) {

            $param = input('post.');

            $agentUserService = new AgentUserService();
            return json($agentUserService->updateAgentUser($param));
        }

        if (request()->isAjax()) {

            $agentLevelService = new AgentLevelService();
            return $agentLevelService->getAllLevel();
        }
    }

    /**
     * 设置分销员
     */
    public function set()
    {
        if (request()->isPost()) {
            $userId = input('post.user_id');

            $agentUserService = new AgentUserService();
            return json($agentUserService->setAgentUser($userId));
        }
    }
}
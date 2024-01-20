<?php


namespace addons\agent\controller\admin;

use addons\agent\service\AgentLevelService;
use app\PluginBaseController;

class AgentLevel extends PluginBaseController
{
    /**
     * 分销等级列表
     */
    public function index()
    {
        if (request()->isAjax()) {

            $param = input('param.');

            $agentLevelService = new AgentLevelService();
            return json($agentLevelService->getLevelList($param));
        }

        return fetch();
    }

    /**
     * 添加分销等级
     */
    public function add()
    {
        if (request()->isPost()) {

            $param = input('post.');

            $agentLevelService = new AgentLevelService();
            return json($agentLevelService->addLevel($param));
        }
    }

    /**
     * 编辑分销等级
     */
    public function edit()
    {
        if (request()->isPost()) {

            $param = input('post.');

            $agentLevelService = new AgentLevelService();
            return json($agentLevelService->editLevel($param));
        }
    }

    /**
     * 分销等级删除
     */
    public function del()
    {
        $id = input('param.id');

        $agentLevelService = new AgentLevelService();
        return json($agentLevelService->delLevel($id));
    }
}
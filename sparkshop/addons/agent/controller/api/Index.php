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

namespace addons\agent\controller\api;

use addons\agent\service\AgentService;
use addons\agent\service\AgentUserService;
use addons\agent\service\AgentWithdrawalService;
use app\api\controller\Base;

class Index extends Base
{
    /**
     * 获取海报二维码
     */
    public function getQrCode()
    {
        $param = input('post.');

        $agentService = new AgentService();
        return json($agentService->getQrcode($param));
    }

    /**
     * 获取分销基本信息
     */
    public function getAgentInfo()
    {
        $agentService = new AgentService();
        return json($agentService->getAgentInfo());
    }

    /**
     * 获取推广用户
     */
    public function getAgentUsers()
    {
        $param = input('param.');

        $agentService = new AgentService();
        return json($agentService->getAgentUsers($param));
    }

    /**
     * 提现申请
     */
    public function apply()
    {
        $param = input('post.');

        $agentWithdrawalService = new AgentWithdrawalService();
        return json($agentWithdrawalService->apply($param));
    }

    /**
     * 提现记录
     */
    public function withdrawal()
    {
        $param = input('param.');

        $agentWithdrawalService = new AgentWithdrawalService();
        return json($agentWithdrawalService->withdrawalLog($param));
    }
}
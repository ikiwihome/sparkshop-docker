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

namespace addons\agent\controller\admin;

use addons\agent\service\AgentGoodsService;
use app\PluginBaseController;

class AgentGoods extends PluginBaseController
{
    /**
     * 分销商品列表
     */
    public function index()
    {
        if (request()->isAjax()) {

            $param = input('param.');

            $agentGoodsService = new AgentGoodsService();
            return json($agentGoodsService->getAgentGoodsList($param));
        }

        return fetch();
    }

    /**
     * 添加分销商品
     */
    public function add()
    {
        if (request()->isPost()) {

            $param = input('post.');

            $agentGoodsService = new AgentGoodsService();
            return json($agentGoodsService->addAgentGoods($param));
        }
    }

    /**
     * 分销商品删除
     */
    public function del()
    {
        $id = input('param.id');

        $agentGoodsService = new AgentGoodsService();
        return json($agentGoodsService->delAgentGoods($id));
    }
}
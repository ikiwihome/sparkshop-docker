<?php


namespace addons\agent\service;

use addons\agent\model\AgentGoods;

class AgentGoodsService
{
    /**
     * 获取等级列表
     * @param array $param
     * @return array
     */
    public function getAgentGoodsList(array $param) : array
    {
        $limit = $param['limit'];
        $where = [];
        if (!empty($param['goods_id'])) {
            $where[] = ['goods_id', '=', $param['goods_id']];
        }

        $agentGoodsModel = new AgentGoods();
        $list = $agentGoodsModel->with(['goods'])->where($where)->paginate($limit);

        return dataReturn(0, 'success', $list);
    }

    /**
     * 增加分销商品
     * @param array $param
     * @return array
     */
    public function addAgentGoods(array $param) : array
    {
        $agentGoodsModel = new AgentGoods();

        $has = $agentGoodsModel->getAllList([
            ['goods_id', 'in', $param['goods_id']]
        ], 'goods_id')['data'];

        $alreadyAddGoods = [];
        foreach ($has as $vo) {
            $alreadyAddGoods[] = $vo['goods_id'];
        }

        $needAddGoods = array_diff($param['goods_id'], $alreadyAddGoods);
        $goodsMap = [];
        if (!empty($needAddGoods)) {

            foreach ($needAddGoods as $vo) {
                $goodsMap[] = [
                    'goods_id' => $vo,
                    'create_time' => now()
                ];
            }

            $agentGoodsModel->insertBatch($goodsMap);
        }

        return dataReturn(0, '添加成功');
    }

    /**
     * 删除分销商品
     * @param int $id
     * @return array
     */
    public function delAgentGoods(int $id) : array
    {
        $agentGoodsModel = new AgentGoods();
        return $agentGoodsModel->delById($id);
    }
}
<?php


namespace addons\agent\service;

use addons\agent\model\AgentOrder;
use addons\agent\model\AgentUser;
use app\model\order\Order;
use app\model\user\User;
use think\facade\Db;

class AgentUserService
{
    /**
     * 获取分销用户列表
     * @param array $param
     * @return array
     */
    public function getAgentUserList(array $param) : array
    {
        $limit = $param['limit'];
        $where = [];
        if (!empty($param['user_id'])) {
            $where[] = ['user_id', '=', $param['user_id']];
        }

        $agentUserModel = new AgentUser();
        $list = $agentUserModel->with(['level', 'parent', 'user'])->where($where)->paginate($limit)->each(function ($item) {

            $item->total_order_amount = round($item->agent_order_amount + $item->self_buy_amount, 2);
            return $item;
        });

        $agentOrder = new AgentOrder();

        $census = [
            'total_user' => $agentUserModel->count('id'),
            'agent_user' => (new User())->where('is_agent', 1)->count('id'),
            'order_num' => $agentOrder->count('id'),
            'order_amount' => round($agentUserModel->sum('agent_order_amount') + $agentUserModel->sum('self_buy_amount'), 2),
            'draw_num' => $agentUserModel->sum('draw_num'),
            'residue_amount' => $agentUserModel->sum('residue_amount')
        ];

        return dataReturn(0, 'success', [
            'list' => $list,
            'census' => $census
        ]);
    }

    /**
     * 检测并添加分销用户
     * @param array $param
     * @param array $config
     * @return array
     */
    public function checkAndAddUser(array $param, array $config) : array
    {
        $canInsert = false;
        // 判定分销模式
        if ($config['agent_type'] == 2) {
            $canInsert = true;
        } else if ($config['agent_type'] == 3) {
            // 获取当前用户的累计消费金额 TODO 单量非常大，以及订单分表的话，该处需要优化。可以累计一个总花费
            $totalSpend = (new Order())->where('user_id', $param['user_id'])->where('pay_status', 2)->sum('pay_price');
            if ($totalSpend >= $config['agent_low_amount']) {
                $canInsert = true;
            }
        } else if ($config['agent_type'] == 4 && !empty($param['goods_id'])) {
            $appointGoods = json_decode($config['buy_designated_goods'], true);
            $goodsMap = [];
            foreach ($appointGoods as $vo) {
                $goodsMap[] = $vo['id'];
            }

            // 此次购买的完全包含设定的商品
            $diff = array_diff($goodsMap, $param['goods_id']);
            if (empty($diff)) {
                $canInsert = true;
            }
        }

        if ($canInsert) {

            $this->initUser($param['user_id']);

            (new User())->updateById([
                'is_agent' => 1,
                'agent_bind_time' => now()
            ], $param['user_id']);
        }

        return dataReturn(0, 'success', $canInsert);
    }

    /**
     * 获取下级人员信息
     * @param array $param
     * @return array
     */
    public function getLowerAgentUser(array $param) : array
    {
        $userModel = new User();
        $list = $userModel->where('parent_spread_id', $param['parent_spread_id'])->paginate($param['limit'])->each(function ($item) use($userModel) {
            return $item->agent_user_num = $userModel->where('parent_spread_id', $item->id)->count('id');
        });

        return dataReturn(0, 'success', $list);
    }

    /**
     * 推广订单列表
     * @param array $param
     * @return array
     */
    public function getAgentOrderList(array $param) : array
    {
        $agentUserOrderModel = new AgentOrder();
        $list = $agentUserOrderModel->with(['agentUser'])->where('agent_user_id', $param['user_id'])->paginate($param['limit']);

        return dataReturn(0, 'success', $list);
    }

    /**
     * 取消分销员资格
     * @param int $userId
     * @return array
     */
    public function cancelAgentUser(int $userId) : array
    {
        (new User())->where('id', $userId)->update([
            'is_agent' => 2
        ]);

        (new AgentUser())->where('user_id', $userId)->delete();

        return dataReturn(0, '操作成功');
    }

    /**
     * 修改分销用户等级
     * @param array $param
     * @return array
     */
    public function updateAgentUser(array $param) : array
    {
        (new AgentUser())->updateByWhere(['level_id' => $param['level_id']], ['user_id' => $param['user_id']]);

        return dataReturn(0, '操作成功');
    }

    /**
     * 设置分销员
     * @param int $userId
     * @return array
     */
    public function setAgentUser(int $userId) : array
    {
        $config = getConfByType('agent');

        $canAgent = $config['agent_type'] == 1 || $config['agent_type'] == 2;
        if (!$canAgent) {
            return dataReturn(-1, '无法指定分销员');
        }

        Db::startTrans();
        try {

            (new User())->where('id', $userId)->update([
                'is_agent' => 1,
                'agent_bind_time' => now()
            ]);

            $this->initUser($userId);

            Db::commit();
        } catch(\Exception $e) {
            Db::rollback();
            return dataReturn(-2, $e->getMessage());
        }

        return dataReturn(0, '操作成功');
    }

    public function initUser(int $userId)
    {
        (new AgentUser())->insert([
            'user_id' => $userId,
            'level_id' => 1,
            'parent_spread_id' => 0,
            'spread_num' => 0,
            'bind_time' => now(),
            'agent_order_num' => 0, // 分销订单量
            'agent_order_amount' => 0,
            'agent_pay_amount' => 0,
            'total_bonus' => 0,
            'self_buy_num' => 0,
            'self_buy_amount' => 0,
            'self_buy_pay_amount' => 0,
            'draw_amount' => 0,
            'draw_num' => 0,
            'residue_amount' => 0,
            'create_time' => now()
        ]);
    }
}
<?php


namespace addons\agent\service;

use addons\agent\model\AgentLevel;
use addons\agent\model\AgentUser;

class AgentLevelUp
{
    // 升级条数
    protected $levelUpTips = [];
    // 升级方式
    protected $levelUpWay = 0;
    // 当前用户信息
    protected $agentUserInfo = [];
    // 是否满足了条件
    protected $match = false;

    /**
     * 推销员升级检测
     * @param int $userId
     * @return array $param
     */
    public function checkLevelUp(int $userId) : array
    {
        $agentUserModel = new AgentUser();
        $agentUserInfo = $agentUserModel->findOne([
            'user_id' => $userId
        ])['data'];

        if (empty($agentUserInfo)) {
            return dataReturn(-1, '用户信息有误');
        }

        // 检测要升级的等级
        $levelInfo = (new AgentLevel())->getAllList([], '*', 'level asc')['data'];
        $nowLevel = [];
        $nextLevel = [];

        foreach ($levelInfo as $vo) {
            if (!empty($nowLevel)) {
                $nextLevel = $vo;
                break;
            }

            if ($vo['id'] == $agentUserInfo['level_id']) {
                $nowLevel = $vo;
            }
        }

        if (empty($nextLevel)) {
            return dataReturn(-10, '无可用等级');
        }

        // 检测升级条件
        $tips =  json_decode($nextLevel['config'], true);
        foreach ($tips as $vo) {
            $this->levelUpTips[$vo['field']] = $vo['value'];
        }
        $this->levelUpWay = $nextLevel['level_up_way'];
        $this->agentUserInfo = $agentUserInfo;

        $checkRes = $this->startCheck();
        if ($checkRes['code'] != 0) {
            return $checkRes;
        }

        $agentUserModel->updateById([
            'level_id' => $nextLevel['id'],
            'update_time' => now()
        ], $agentUserInfo['id']);

        return dataReturn(0, '升级完成');
    }

    /**
     * 开始检测
     * @return array
     */
    protected function startCheck() : array
    {
        return $this->agentNum();
    }

    /**
     * 提成单数
     * @return array
     */
    protected function agentNum() : array
    {
        // 条件中不包含提成单数
        if (!isset($this->levelUpTips['agent_num'])) {
            return $this->agentAmount();
        }

        $this->match = true;
        $totalOrderNum = $this->agentUserInfo['agent_order_num'] + $this->agentUserInfo['self_buy_num'];
        if ($totalOrderNum >= $this->levelUpTips['agent_num']) {
            // 满足任意条件
            if ($this->levelUpWay == 1) {
                return dataReturn(0, '满足升级条件');
            } else if ($this->levelUpWay == 2) { // 需要全部满足
                return $this->agentAmount();
            }
        } else {
            // 满足任意条件
            if ($this->levelUpWay == 1) {
                return $this->agentAmount();
            }
        }

        return dataReturn(-2, '不满足升级条件');
    }

    /**
     * 提成金额
     * @return array
     */
    protected function agentAmount() : array
    {
        // 条件中不包含提成金额
        if (!isset($this->levelUpTips['agent_amount'])) {
            return $this->selfBuyNum();
        }

        $this->match = true;
        $totalAmount =  $this->agentUserInfo['agent_order_amount'] + $this->agentUserInfo['self_buy_amount'];
        if ($totalAmount >= $this->levelUpTips['agent_amount']) {
            // 满足任意条件
            if ($this->levelUpWay == 1) {
                return dataReturn(0, '满足升级条件');
            } else if ($this->levelUpWay == 2) { // 需要全部满足
                return $this->selfBuyNum();
            }
        } else {
            // 满足任意条件
            if ($this->levelUpWay == 1) {
                return $this->selfBuyNum();
            }
        }

        return dataReturn(-3, '不满足升级条件');
    }

    /**
     * 自购单数
     * @return array
     */
    protected function selfBuyNum() : array
    {
        // 条件中不包含自购单数
        if (!isset($this->levelUpTips['self_buy_num'])) {
            return $this->selBuyAmount();
        }

        $this->match = true;
        if ($this->agentUserInfo['self_buy_num'] >= $this->levelUpTips['self_buy_num']) {
            // 满足任意条件
            if ($this->levelUpWay == 1) {
                return dataReturn(0, '满足升级条件');
            } else if ($this->levelUpWay == 2) { // 需要全部满足
                return $this->selBuyAmount();
            }
        } else {
            // 满足任意条件
            if ($this->levelUpWay == 1) {
                return $this->selBuyAmount();
            }
        }

        return dataReturn(-4, '不满足升级条件');
    }

    /**
     * 自购金额
     * @return array
     */
    protected function selBuyAmount() : array
    {
        // 条件中不包含自购金额
        if (!isset($this->levelUpTips['self_buy_amount'])) {
            return $this->agentPersonNum();
        }

        $this->match = true;
        if ($this->agentUserInfo['self_buy_amount'] >= $this->levelUpTips['self_buy_amount']) {
            // 满足任意条件
            if ($this->levelUpWay == 1) {
                return dataReturn(0, '满足升级条件');
            } else if ($this->levelUpWay == 2) { // 需要全部满足
                return $this->agentPersonNum();
            }
        } else {
            // 满足任意条件
            if ($this->levelUpWay == 1) {
                return $this->agentPersonNum();
            }
        }

        return dataReturn(-5, '不满足升级条件');
    }

    /**
     * 分销人数
     * @return array
     */
    protected function agentPersonNum() : array
    {
        // notice 此条件当最后一个条件，这里最后一个条件要做特殊的处理
        if (!isset($this->levelUpTips['agent_person_num']) && !$this->match) {
            return dataReturn(-5, '条件异常无匹配条件');
        }

        $totalAgentPersonNum = $this->agentUserInfo['spread_num'];
        if ($totalAgentPersonNum >= $this->levelUpTips['agent_person_num']) {
            return dataReturn(0, '满足升级条件');
        }

        return dataReturn(-6, '不满足升级条件');
    }
}
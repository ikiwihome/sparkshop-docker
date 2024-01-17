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

namespace addons\agent\service;

use addons\agent\model\AgentLevel;
use addons\agent\model\AgentUser;
use addons\agent\validate\AgentLevelValidate;
use think\exception\ValidateException;

class AgentLevelService
{
    /**
     * 获取等级列表
     * @param array $param
     * @return array
     */
    public function getLevelList(array $param) : array
    {
        $limit = $param['limit'];
        $where = [];

        $agentLevelModel = new AgentLevel();
        $list = $agentLevelModel->getPageList($limit, $where, '*', 'level asc')['data']->each(function ($item) {
            $tips = '';
            $config = json_decode($item->config, true);

            foreach ($config as $vo) {
                if ($item->level_up_way == 1) {
                    $tips .= $vo['name'] . '大于等于' . $vo['value'] . ' 或者 ';
                } else {
                    $tips .= $vo['name'] . '大于等于' . $vo['value'] . ' 并且 ';
                }
            }

            if ($item->level_up_way == 1) {
                $item->tips = rtrim($tips, ' 或者 ');
            } else {
                $item->tips = rtrim($tips, ' 并且 ');
            }

            return $item;
        });

        return dataReturn(0, 'success', $list);
    }

    /**
     * 增加分销等级
     * @param array $param
     * @return array
     */
    public function addLevel(array $param) : array
    {
        try {
            validate(AgentLevelValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $agentLevelModel = new AgentLevel();
        // 检测唯一
        $has = $agentLevelModel->findOne(['level' => $param['level']], 'id')['data'];
        if (!empty($has)) {
            return dataReturn(-2, '该等级已经存在');
        }

        $param['config'] = json_encode($param['config']);
        $param['create_time'] = now();

        return $agentLevelModel->insertOne($param);
    }

    /**
     * 编辑等级
     * @param array $param
     * @return array
     */
    public function editLevel(array $param) : array
    {
        try {
            validate(AgentLevelValidate::class)->scene('edit')->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        // 默认等级
        if ($param['id'] != 1 && empty($param['config'])) {
            return dataReturn(-3, '条件不能为空');
        }

        $agentLevelModel = new AgentLevel();
        // 检测唯一
        $has = $agentLevelModel->findOne([
            ['level', '=', $param['level']],
            ['id', '<>', $param['id']],
        ], 'id')['data'];
        if (!empty($has)) {
            return dataReturn(-2, '该等级已经存在');
        }

        $param['config'] = json_encode($param['config']);
        $param['update_time'] = now();

        return $agentLevelModel->updateById($param, $param['id']);
    }

    /**
     * 删除等级
     * @param int $id
     * @return array
     */
    public function delLevel(int $id) : array
    {
        $agentLevelModel = new AgentLevel();
        $info = $agentLevelModel->findOne(['id' => $id])['data'];

        $has = (new AgentUser())->findOne(['level_id' => $info['id']])['data'];
        if (!empty($has)) {
            return dataReturn(-1, '该等级下有分销人员，不可删除');
        }

        return $agentLevelModel->delById($id);
    }

    /**
     * 获取分销等级
     * @return array
     */
    public function getAllLevel() : array
    {
        $agentLevelModel = new AgentLevel();

        $levelList = $agentLevelModel->getAllList([], 'id,level,name', 'level asc')['data'];
        return dataReturn(0, 'success', $levelList);
    }
}
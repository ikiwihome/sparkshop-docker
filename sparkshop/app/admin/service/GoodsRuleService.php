<?php

namespace app\admin\service;

use app\model\goods\Goods;
use app\model\goods\GoodsRule;
use app\model\goods\GoodsRuleExtend;
use app\model\goods\GoodsRuleTpl;
use think\db\exception\DbException;

class GoodsRuleService
{
    /**
     * 获取商品列表
     * @param array $param
     * @return array
     * @throws DbException
     */
    public function getList(array $param): array
    {
        $limit = $param['limit'];
        $name = $param['name'];

        $where = [];
        if (!empty($name)) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }

        $goodsRuleModel = new GoodsRuleTpl();
        $list = $goodsRuleModel->where($where)->order('id desc')->paginate($limit)->each(function ($item, $key) {
            $data = json_decode($item['value'], true);
            $title = '';
            $items = '';
            foreach ($data as $vo) {
                $title .= $vo['title'] . ',';
                $items .= implode(',', $vo['item']) . ' | ';
            }

            $item->title = rtrim($title, ',');
            $item->item = rtrim($items, ' | ');
        });

        return dataReturn(0, 'success', $list);
    }

    /**
     * 添加商品规格
     * @param array $param
     * @return array
     */
    public function addGoodsRule(array $param): array
    {
        $name = $param['name'];
        unset($param['name'], $param['id']);

        $goodsRuleModel = new GoodsRuleTpl();
        $has = $goodsRuleModel->checkUnique([
            'name' => $name
        ])['data'];

        if (!empty($has)) {
            return dataReturn(-2, '该模板名称已经存在');
        }

        // 检测数据的合法性
        $value = [];
        $uniqueItemName = [];
        foreach ($param as $vo) {
            if (isset($uniqueItemName[$vo['title']])) {
                return dataReturn(-3, '规则名' . $vo['title'] . '已经存在');
            }

            if (!isset($vo['item'])) {
                return dataReturn(-4, '规则名' . $vo['title'] . '的规则值缺失，请补充');
            }

            $uniqueItemName[$vo['title']] = 1;
            $itemsLen = count($vo['item']);
            if ($itemsLen > array_unique($vo['item'])) {
                return dataReturn(-4, '规则名' . $vo['title'] . '的规则值重复，请认真检查');
            }

            $value[] = $vo;
        }

        $addParam['name'] = $name;
        $addParam['value'] = json_encode($value);
        return $goodsRuleModel->insertOne($addParam);
    }

    /**
     * 编辑商品规则
     * @param array $param
     * @return array
     */
    public function editGoodsRule(array $param): array
    {
        $id = $param['id'];
        $name = $param['name'];
        unset($param['name'], $param['id']);

        $goodsRuleModel = new GoodsRuleTpl();

        $where[] = ['id', '<>', $id];
        $where[] = ['name', '=', $name];
        $has = $goodsRuleModel->checkUnique($where)['data'];

        if (!empty($has)) {
            return dataReturn(-2, '规则名已经存在');
        }

        // 检测数据的合法性
        $value = [];
        $uniqueItemName = [];
        foreach ($param as $vo) {

            if (isset($uniqueItemName[$vo['title']])) {
                return dataReturn(-3, '规则名' . $vo['title'] . '已经存在');
            }

            if (!isset($vo['item'])) {
                return dataReturn(-4, '规则名' . $vo['title'] . '的规则值缺失，请补充');
            }

            $uniqueItemName[$vo['title']] = 1;
            $itemsLen = count($vo['item']);
            if ($itemsLen > array_unique($vo['item'])) {
                return dataReturn(-4, '规则名' . $vo['title'] . '的规则值重复，请认真检查');
            }

            $value[] = $vo;
        }

        $editParam['name'] = $name;
        $editParam['value'] = json_encode($value);

        return $goodsRuleModel->updateById($editParam, $id);
    }

    /**
     * 根据商品id获取规格
     * @param int $goodsId
     * @return array
     */
    public function getRuleByGoodsId(int $goodsId): array
    {
        $goodsRuleModel = new GoodsRule();
        $goodsRuleExtendModel = new GoodsRuleExtend();
        $goodsModel = new Goods();

        return dataReturn(0, 'success', [
            'ruleData' => $goodsRuleModel->findOne([
                    'goods_id' => $goodsId
                ])['data'] ?? '{}',
            'goods_info' => $goodsModel->findOne([
                'id' => $goodsId
            ], 'stock')['data'],
            'extend' => $goodsRuleExtendModel->getAllList([
                    'goods_id' => $goodsId
                ], '*', 'id asc')['data'] ?? '{}'
        ]);
    }
}
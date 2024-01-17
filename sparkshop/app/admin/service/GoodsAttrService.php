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
namespace app\admin\service;

use app\model\goods\GoodsAttrTpl;
use think\db\exception\DbException;

class GoodsAttrService
{
    /**
     * 获取商品属性列表
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

        $goodsAttrTplModel = new GoodsAttrTpl();
        $list = $goodsAttrTplModel->where($where)->order('id desc')->paginate($limit)->each(function ($item, $key) {
            $item->attr = json_decode($item->value, true);
        });

        return dataReturn(0, 'success', $list);
    }

    /**
     * 添加商品属性
     * @param array $param
     * @return array
     */
    public function addGoodsAttr(array $param): array
    {
        $name = $param['name'];
        unset($param['name']);

        $goodsAttrTplModel = new GoodsAttrTpl();
        $has = $goodsAttrTplModel->checkUnique([
            'name' => $name
        ])['data'];

        if (!empty($has)) {
            return dataReturn(-2, '该模板名称已经存在');
        }

        $addParam['name'] = $name;
        $addParam['value'] = json_encode($param);

        return $goodsAttrTplModel->insertOne($addParam);
    }

    /**
     * 编辑商品属性
     * @param array $param
     * @return array
     */
    public function editGoodsAttr(array $param): array
    {
        $name = $param['name'];
        $id = $param['id'];
        unset($param['name'], $param['id']);

        $goodsAttrTplModel = new GoodsAttrTpl();

        $where[] = ['id', '<>', $id];
        $where[] = ['name', '=', $name];
        $has = $goodsAttrTplModel->checkUnique($where)['data'];

        if (!empty($has)) {
            return dataReturn(-2, '属性名已经存在');
        }

        $editParam['name'] = $name;
        $editParam['value'] = json_encode($param);

        return $goodsAttrTplModel->updateById($editParam, $id);
    }
}
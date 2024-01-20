<?php

namespace app\admin\service;

use app\admin\validate\GoodsCateValidate;
use app\model\goods\Goods;
use app\model\goods\GoodsCate;
use think\exception\ValidateException;

class GoodsCateService
{
    /**
     * 添加商品分类
     * @param array $param
     * @return array
     */
    public function addGoodsCate(array $param): array
    {
        if (isset($param['file'])) {
            unset($param['file']);
        }

        // 检验完整性
        try {
            validate(GoodsCateValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $goodsCateModel = new GoodsCate();
        $has = $goodsCateModel->checkUnique([
            'name' => $param['name']
        ])['data'];

        if (!empty($has)) {
            return dataReturn(-2, '分类名已经存在');
        }

        if ($param['pid'] == 0) {
            $param['level'] = 1;
        } else {
            $parentInfo = $goodsCateModel->findOne([
                'id' => $param['pid']
            ], 'pid')['data'];

            if ($parentInfo['pid'] == 0) {
                $param['level'] = 2;
            } else {
                $param['level'] = 3;
            }
        }

        $param['create_time'] = now();
        return $goodsCateModel->insertOne($param);
    }

    /**
     * 编辑商品分类
     * @param array $param
     * @return array
     */
    public function editGoodsCate(array $param): array
    {
        if (isset($param['file'])) {
            unset($param['file']);
        }

        // 检验完整性
        try {
            validate(GoodsCateValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $goodsCateModel = new GoodsCate();

        $where[] = ['id', '<>', $param['id']];
        $where[] = ['name', '=', $param['name']];
        $has = $goodsCateModel->checkUnique($where)['data'];

        if (!empty($has)) {
            return dataReturn(-2, '分类名已经存在');
        }

        // 处理子分类隐藏
        $goodsCateModel->updateByWhere([
            'status' => $param['status'],
            'update_time' => now()
        ], [
            'pid' => $param['id']
        ]);

        return $goodsCateModel->updateById($param, $param['id']);
    }

    /**
     * 删除商品分类
     * @param int $id
     * @return array
     */
    public function delGoodsCate(int $id): array
    {
        // 查询是否有商品使用否则不让删
        $goodsModel = new Goods();
        $goodsInfo = $goodsModel->findOne([
            'cate_id' => $id,
            'is_del' => 2
        ], 'id')['data'];
        if (!empty($goodsInfo)) {
            return dataReturn(-1, '有商品在使用该分类，不可删除');
        }

        $goodsCateModel = new GoodsCate();
        $has = $goodsCateModel->findOne([
            'pid' => $id
        ], 'id')['data'];
        if (!empty($has)) {
            return dataReturn(-2, '该分类下有子分类，不可删除');
        }

        $goodsCateModel = new GoodsCate();
        return $goodsCateModel->delById($id);
    }
}
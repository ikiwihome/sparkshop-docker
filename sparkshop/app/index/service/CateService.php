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

namespace app\index\service;

use app\model\goods\Goods;
use app\model\goods\GoodsCate;
use think\facade\Route;

class CateService
{
    /**
     * 分类列表
     * @param int $cateId
     * @param int $level
     * @param int $limit
     * @param string $sortPrice
     * @return array|string
     */
    public function getCateList(int $cateId, int $level, int $limit, string $sortPrice)
    {
        // 该分类信息
        $cateModel = new GoodsCate();
        $firstLevelCateList = $cateModel->getAllList([
            'status' => 1,
            'pid' => 0
        ], 'id,name')['data'];

        if ($cateId == 0) {
            $cateId = $firstLevelCateList[0]['id'];
        }

        $cateInfo = $cateModel->findOne(['id' => $cateId], 'id,name,pid')['data'];
        if ($level == 2) {
            $goodsCateId = $cateId;
            $cateInfo = $cateModel->findOne(['id' => $cateInfo['pid']], 'id,name,pid')['data'];
        } else {
            $goodsCateId = $cateModel->field('id')->where([
                'pid' => $cateId,
                'status' => 1
            ])->order('id desc')->findOrEmpty()->id;
        }

        $secondLevelCateList = $cateModel->getAllList([
            'status' => 1,
            'pid' => $cateInfo['id']
        ], 'id,name')['data'];

        // 关联的商品信息
        $goodsModel = new Goods();
        $where[] = ['cate_id', '=', $goodsCateId];
        $where[] = ['is_del', '=', 2];
        $where[] = ['is_show', '=', 1];

        $order = 'id desc';
        if (!empty($sortPrice)) {
            $order = 'price ' . $sortPrice;
        }

        $goodsList = $goodsModel->field('id,name,slider_image,sales,collects,price')->where($where)->order($order)
            ->paginate([
                'list_rows'=> $limit,
                'query' => [
                    'p_s' => $sortPrice
                ],
            ])->each(function ($item) {
                $item->slider_image = json_decode($item->slider_image, true)['0'];
            });

        return dataReturn(0, 'success', [
            'cate_id' => $goodsCateId,
            'first_cate_list' => $firstLevelCateList,
            'second_level_cate_list' => $secondLevelCateList,
            'goods_list' => $goodsList->toArray(),
            'pages' => $goodsList->render(),
            'level' => $level,
            'price_type' => $sortPrice,
            'search' => '',
            'cateInfo' => $cateInfo,
        ]);
    }

    /**
     * 搜索
     * @param string $search
     * @return array
     */
    public function searchGoods(string $search): array
    {
        // 关联的商品信息
        $goodsModel = new Goods();
        $where[] = ['name', 'like', '%' . $search . '%'];
        $where[] = ['is_del', '=', 2];
        $where[] = ['is_show', '=', 1];

        $goodsList = $goodsModel->getPageList(20, $where, 'id,name,slider_image,sales,collects,price')['data']
            ->each(function ($item) {
                $item->slider_image = json_decode($item->slider_image, true)['0'];
            });

        return dataReturn(0, 'success', [
            'cate_id' => 0,
            'first_cate_list' => [],
            'second_level_cate_list' => [],
            'goods_list' => $goodsList->toArray(),
            'pages' => $goodsList->render(),
            'level' => 0,
            'price_type' => '',
            'search' => $search,
            'cateInfo' => [
                'id' => 0,
                'name' => $search
            ],
        ]);
    }
}
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
namespace app\api\controller;

use app\BaseController;
use app\service\GoodsService;

class Goods extends BaseController
{
    public function initialize()
    {
        crossDomain();
    }

    /**
     * 商品详情
     */
    public function detail(GoodsService $goodsService)
    {
        return json($goodsService->getMobileGoodsDetail(input('param.id')));
    }

    /**
     * 商品规格详情
     */
    public function goodsRuleDetail(GoodsService $goodsService)
    {
        return json($goodsService->getGoodsRuleDetail(input('param.sku'), input('param.goods_id')));
    }

    /**
     * 获取商品评论
     */
    public function getComments(GoodsService $goodsService)
    {
        return json($goodsService->getComments(input('param.')));
    }

    /**
     * 获取分类下的商品
     */
    public function getGoodsByCateInfo(GoodsService $goodsService)
    {
        return json($goodsService->getGoodsByCateId(input('param.')));
    }
}
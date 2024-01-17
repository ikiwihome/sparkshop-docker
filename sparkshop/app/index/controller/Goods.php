<?php
// +----------------------------------------------------------------------
// | SparkShop 坚持做优秀的商城系统
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2099 http://sparkshop.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai
// +----------------------------------------------------------------------

namespace app\index\controller;

use app\service\GoodsService;
use think\facade\View;

class Goods extends Base
{
    /**
     * 商品详情页
     */
    public function detail(GoodsService $goodsService)
    {
        $goodsId = input('param.id');
        $type = input('param.type', 1);

        $res = $goodsService->getGoodsDetail($goodsId);
        if ($res['code'] != 0) {
            return build404($res);
        }

        View::assign($res['data']);
        View::assign(['type' => $type]);

        return View::fetch();
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
        if (request()->isAjax()) {

            return json($goodsService->getComments(input('param.')));
        }
    }
}
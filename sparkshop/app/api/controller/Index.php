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

use app\api\service\IndexService;
use app\BaseController;
use app\service\CartService;
use app\service\OrderService;

class Index extends BaseController
{
    public function initialize()
    {
        crossDomain();
    }

    /**
     * 小程序首页
     */
    public function index(IndexService $indexService)
    {
        return json($indexService->getIndexData());
    }

    /**
     * 搜索
     */
    public function search(IndexService $indexService)
    {
        return json($indexService->search(input('post.')));
    }

    /**
     * 获取营销插件的配置
     */
    public function marketingConfig(OrderService $orderService)
    {
        return json($orderService->getMarketingConfig(input('param.goods_id', 0)));
    }

    /**
     * 获取购物车数量
     */
    public function cartNum(CartService $cartService)
    {
        return json($cartService->getCartNum());
    }
}
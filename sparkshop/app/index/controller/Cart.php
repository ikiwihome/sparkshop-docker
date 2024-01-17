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

use app\service\CartService;
use think\facade\View;

class Cart extends Base
{
    public function initialize()
    {
        parent::initialize();
        pcLoginCheck();
    }

    /**
     * 加入购物车
     */
    public function add(CartService $cartService)
    {
        return json($cartService->addCart(input('post.'), session('home_user_id')));
    }

    /**
     * 购物车详情
     */
    public function detail(CartService $cartService)
    {
        if (request()->isAjax()) {

            return json($cartService->detail(input('param.limit', 1), session('home_user_id')));
        }

        return View::fetch();
    }

    /**
     * 移除购物车
     */
    public function remove(CartService $cartService)
    {
        return json($cartService->removeCartGoods(input('param.id'), session('home_user_id')));
    }
}
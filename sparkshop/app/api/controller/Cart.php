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

use app\service\CartService;

class Cart extends Base
{
    /**
     * 我的购车列表
     */
    public function list(CartService $cartService)
    {
        return json($cartService->detail(input('param.limit'), $this->user['id']));
    }

    /**
     * 添加购物车
     */
    public function add(CartService $cartService)
    {
        return json($cartService->addCart(input('post.'), $this->user['id']));
    }

    /**
     * 移除购物车
     */
    public function remove(CartService $cartService)
    {
        return json($cartService->removeCartGoods(input('param.id'), $this->user['id']));
    }

    /**
     * 清空购物车
     */
    public function clearCart(CartService $cartService)
    {
        return json($cartService->clearCart($this->user['id']));
    }

    /**
     * 改变购物车内商品数量
     */
    public function changeNum(CartService $cartService)
    {
        $param = input('post.');
        $param['user_id'] = $this->user['id'];

        return json($cartService->changeNum($param));
    }
}
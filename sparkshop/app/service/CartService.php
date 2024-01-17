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
namespace app\service;

use app\model\goods\Goods;
use app\model\goods\GoodsRuleExtend;
use app\model\system\Cart;
use think\db\exception\DbException;

class CartService
{
    /**
     * 添加购物车
     * @param array $param
     * @param int $userId
     * @return array
     */
    public function addCart(array $param, int $userId): array
    {
        try {

            $goodsModel = new Goods();
            $goodsInfo = $goodsModel->where('id', $param['goods_id'])->where('is_del', 2)->find();
            if (empty($goodsInfo)) {
                return dataReturn(-1, "该商品不存在");
            }

            $cartModel = new Cart();
            // 多规格
            if ($goodsInfo['spec'] == 2) {

                $goodsRuleExtendModel = new GoodsRuleExtend();
                $ruleInfo = $goodsRuleExtendModel->where('goods_id', $param['goods_id'])
                    ->where('sku', implode('※', $param['rule']))->find();

                if (empty($ruleInfo)) {
                    return dataReturn(-2, "该商品不存在");
                }

                if ($ruleInfo['stock'] <= 0) {
                    return dataReturn(-3, "该商品库存不足");
                }

                // 查询购物车是否有相同的商品
                $hasGoods = $cartModel->findOne([
                    'user_id' => $userId,
                    'goods_id' => $param['goods_id'],
                    'rule_id' => $ruleInfo['id']
                ], 'id,goods_num')['data'];

                if (!empty($hasGoods)) {

                    $cartModel->updateById([
                        'goods_num' => $hasGoods['goods_num'] + $param['num'],
                        'update_time' => now()
                    ], $hasGoods['id']);
                } else {

                    $param = [
                        'user_id' => $userId,
                        'goods_id' => $param['goods_id'],
                        'title' => $goodsInfo['name'],
                        'images' => $ruleInfo['image'],
                        'original_price' => $ruleInfo['original_price'],
                        'price' => $ruleInfo['price'],
                        'goods_num' => $param['num'],
                        'total_amount' => $param['num'] * $ruleInfo['price'],
                        'rule_id' => $ruleInfo['id'],
                        'rule_text' => implode(' ', $param['rule']),
                        'create_time' => now()
                    ];

                    $res = $cartModel->insertOne($param);
                    if ($res['code'] != 0) {
                        return $res;
                    }
                }
            } else { // 单规格

                // 查询购物车是否有相同的商品
                $hasGoods = $cartModel->findOne([
                    'user_id' => $userId,
                    'goods_id' => $param['goods_id'],
                ], 'id,goods_num')['data'];

                if (!empty($hasGoods)) {

                    $cartModel->updateById([
                        'goods_num' => $hasGoods['goods_num'] + $param['num'],
                        'update_time' => now()
                    ], $hasGoods['id']);
                } else {
                    $param = [
                        'user_id' => $userId,
                        'goods_id' => $param['goods_id'],
                        'title' => $goodsInfo['name'],
                        'images' => json_decode($goodsInfo['slider_image'], true)[0],
                        'original_price' => $goodsInfo['original_price'],
                        'price' => $goodsInfo['price'],
                        'goods_num' => $param['num'],
                        'total_amount' => $param['num'] * $goodsInfo['price'],
                        'rule_id' => 0,
                        'create_time' => now()
                    ];

                    $res = $cartModel->insertOne($param);
                    if ($res['code'] != 0) {
                        return $res;
                    }
                }
            }

            $cartNum = $cartModel->where('user_id', $userId)->sum('goods_num');
            $cartAmount = number_format($cartModel->where('user_id', $userId)->sum('total_amount'), 2);

            return  dataReturn(0, "加入成功", compact('cartNum', 'cartAmount'));
        } catch (\Exception $e) {

            return dataReturn(-5, $e->getMessage());
        }
    }

    /**
     * @param int $limit
     * @param int $userId
     * @return array
     */
    public function detail(int $limit, int $userId): array
    {
        try {

            $cartModel = new Cart();
            $cartList = $cartModel->where('user_id', $userId)
                ->order('id desc')->paginate($limit);

            return dataReturn(0, "success", $cartList);
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }
    }

    /**
     * 删除购物车物品
     * @param int $id
     * @param int $userId
     * @return array
     */
    public function removeCartGoods(int $id, int $userId): array
    {
        $cartModel = new Cart();
        return $cartModel->delByWhere([
            'id' => $id,
            'user_id' => $userId
        ]);
    }

    /**
     * 购物车数量
     * @return array
     */
    public function getCartNum(): array
    {
        $userInfo = getJWT(getHeaderToken());
        if (empty($userInfo)) {
            return dataReturn(0, 'success', 0);
        }

        $num = (new Cart())->where('user_id', $userInfo['id'])->sum('goods_num');
        return dataReturn(0, 'success', $num);
    }

    /**
     * 清空购物车
     * @param int $userId
     * @return array
     * @throws DbException
     */
    public function clearCart(int $userId): array
    {
        (new Cart())->where('user_id', $userId)->delete();
        return dataReturn(0, '删除成功');
    }

    /**
     * 购物车数量
     * @param int $userId
     * @return array
     */
    public function getUserCartNum(int $userId): array
    {
        $num = (new Cart())->where('user_id', $userId)->sum('goods_num');
        return dataReturn(0, 'success', $num);
    }

    /**
     * 购物车金额
     * @param int $userId
     * @return array
     */
    public function getUserCartAmount(int $userId): array
    {
        $num = (new Cart())->where('user_id', $userId)->sum('total_amount');
        return dataReturn(0, 'success', $num);
    }

    /**
     * 改变商品数量
     * @param array $param
     * @return array
     */
    public function changeNum(array $param) : array
    {
        return (new Cart())->updateByWhere([
            'goods_num' => $param['num']
        ], ['id' => $param['id'], 'user_id' => $param['user_id']]);
    }
}
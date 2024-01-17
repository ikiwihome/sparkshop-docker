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

namespace addons\seckill\controller\index;

use addons\seckill\service\OrderService;
use addons\seckill\service\SeckillService;
use app\BaseController;

class Index extends BaseController
{
    /**
     * 秒杀商品列表
     */
    public function index()
    {
        $param = input('param.');

        $seckillService = new SeckillService();
        return json($seckillService->getSeckillList($param));
    }

    /**
     * 秒杀商品详情
     */
    public function detail()
    {
        $seckillId = input('param.seckill_id');

        $seckillService = new SeckillService();
        return json($seckillService->seckillDetail($seckillId));
    }

    /**
     * 订单试算
     */
    public function trail()
    {
        $param = input('post.');

        $orderService = new OrderService();
        return json($orderService->trail($param, 2));
    }

    /**
     * 创建订单
     */
    public function createOrder()
    {
        $param = input('post.');
        $param['platform'] = '';

        $orderService = new OrderService();
        return json($orderService->createorder($param, 2));
    }
}
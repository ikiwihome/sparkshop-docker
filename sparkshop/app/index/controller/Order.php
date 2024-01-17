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

use app\service\OrderService;
use think\facade\View;

class Order extends Base
{
    public function initialize()
    {
        parent::initialize();
        pcLoginCheck();
    }

    /**
     * 订单
     * @return string
     */
    public function index(OrderService $orderService)
    {
        $param = input('post.');
        $param['user_id'] = session('home_user_id');

        $res = $orderService->prepareOrderParam($param);
        if ($res['code'] != 0) {
            return build404($res);
        }

        View::assign($res['data']);
        $vipConf = getConfByType('shop_user_level');

        View::assign([
            'vipConf' => $vipConf
        ]);

        return View::fetch();
    }

    /**
     * 试算
     */
    public function trial(OrderService $orderService)
    {
        $param = $orderService->doTrial(input('post.'), session('home_user_id'));
        if ($param['code'] != 0) {
            return json($param);
        }

        $param['data']['vipDiscount'] = number_format($param['data']['vipDiscount'], 2);
        $param['data']['coupon'] = number_format($param['data']['coupon']['couponAmount'], 2);
        $param['data']['totalPrice'] = number_format($param['data']['totalPrice'], 2);
        $param['data']['postage'] = number_format($param['data']['postage'], 2);
        $param['data']['realPay'] = number_format($param['data']['realPay'], 2);

        return json($param);
    }

    /**
     * 创建订单
     */
    public function createOrder(OrderService $orderService)
    {
        $param = input('post.');

        if (empty($param['goods'])) {
            return jsonReturn(-11, "购买商品信息错误");
        }

        if (empty($param['pay_way'])) {
            return jsonReturn(-10, "请先开启支付");
        }

        $param['platform'] = ''; // PC端不设置平台
        return json($orderService->createOrder($param, session('home_user_id')));
    }

    /**
     * 检测订单的状态
     */
    public function checkOrderStatus(OrderService $orderService)
    {
        return json($orderService->getOrderStatus(input('param.order')));
    }
}
<?php

namespace app\api\controller;

use app\service\OrderService;
use app\service\UserOrderService;

class UserOrder extends Base
{
    /**
     * 用户订单
     */
    public function index(OrderService $orderService)
    {
        return json($orderService->getUserOrderList(input('param.'), $this->user['id']));
    }

    /**
     * 订单信息
     */
    public function detail(OrderService $orderService)
    {
        return json($orderService->getOrderDetail(input('param.id'), $this->user['id']));
    }

    /**
     * 取消订单
     */
    public function cancel(UserOrderService $orderService)
    {
        return json($orderService->orderCancel(input('param.id'), $this->user['id'], $this->user['name']));
    }

    /**
     * 去支付
     */
    public function goPay(UserOrderService $userOrderService)
    {
        $param = input('post.');
        $param['platform'] = isset(request()->header()['x-csrf-token']) ? request()->header()['x-csrf-token'] : '';

        return json($userOrderService->goPay($param, $this->user['id']));
    }

    /**
     * 确认收货
     */
    public function received(UserOrderService $userOrderService)
    {
        $res = $userOrderService->doReceived(input('param.id'), $this->user['id'], $this->user['name']);
        if ($res['code'] == 0) {
            $res['msg'] = '操作成功';
        }

        return json($res);
    }

    /**
     * 评价
     */
    public function appraise(UserOrderService $userOrderService)
    {
        if (request()->isPost()) {

            return json($userOrderService->doAppraise(input('post.'), $this->user));
        }

        return json($userOrderService->getGoodsComments(input('param.order_id'), input('param.order_detail_id'), $this->user['id']));
    }

    /**
     * 物流信息
     */
    public function express(UserOrderService $userOrderService)
    {
        return json($userOrderService->getExpressInfo(input('param.id'), $this->user['id']));
    }
}
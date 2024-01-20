<?php

namespace app\api\controller;

use app\service\OrderRefundService;
use app\service\UserOrderService;

class OrderRefund extends Base
{
    /**
     * 售后订单列表
     */
    public function index(OrderRefundService $orderRefundService)
    {
        return json($orderRefundService->getRefundList(input('param.')));
    }

    /**
     * 退款试算
     */
    public function refundTrail(UserOrderService $userOrderService)
    {
        $param = input('post.');
        $param['user_id'] = $this->user['id'];

        return json($userOrderService->refundTrail($param));
    }

    /**
     * 申请售后
     */
    public function refund(UserOrderService $userOrderService)
    {
        return json($userOrderService->doRefundOrder(input('post.'), $this->user));
    }

    /**
     * 订单详情
     */
    public function getDetail(OrderRefundService $orderRefundService)
    {
        return json($orderRefundService->getRefundDetail(input('param.refund_id'), $this->user['id']));
    }

    /**
     * 取消退款
     */
    public function cancelRefund(UserOrderService $userOrderService)
    {
        return json($userOrderService->cancelRefund(input('param.id'), $this->user['id']));
    }

    /**
     * 快递信息查询
     */
    public function refundExpress(OrderRefundService $orderRefundService)
    {
        $res = $orderRefundService->doRefundExpress(input('post.'), $this->user['id']);
        if ($res['code'] == 0) {
            $res['msg'] = '操作成功';
        }

        return json($res);
    }
}
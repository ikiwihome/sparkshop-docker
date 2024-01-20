<?php

namespace app\admin\controller;

use app\admin\service\RefundService;
use think\facade\View;

class Refund extends Base
{
    /**
     * 退款列表
     */
    public function index(RefundService $refundService)
    {
        if (request()->isAjax()) {

            return json($refundService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 订单详情
     */
    public function detail(RefundService $refundService)
    {
        return json($refundService->getDetail(input('param.id'), input('param.refund')));
    }

    /**
     * 退货审核
     */
    public function checkRefundGoods(RefundService $refundService)
    {
        $refundService->checkRefundGoods(input('post.'));
        return jsonReturn(0, '操作成功');
    }

    /**
     * 退款审核
     */
    public function checkRefundMoney(RefundService $refundService)
    {
        return json($refundService->checkRefundMoney(input('post.')));
    }
}
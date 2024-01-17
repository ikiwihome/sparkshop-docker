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
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

use app\service\OrderRefundService;
use app\service\UserOrderService;
use think\facade\View;

class UserOrder extends Base
{
    public function initialize()
    {
        parent::initialize();
        pcLoginCheck();
    }

    /**
     * 取消订单
     */
    public function cancel(UserOrderService $userOrderService)
    {
        return json($userOrderService->orderCancel(input('param.id'), session('home_user_id'), session('home_user_name')));
    }

    /**
     * 去支付
     */
    public function goPay(UserOrderService $userOrderService)
    {
        return json($userOrderService->goPay(input('post.'), session('home_user_id')));
    }

    /**
     * 申请售后
     */
    public function refund(UserOrderService $userOrderService)
    {
        // 处理提交售后
        if (request()->isPost()) {

            $param = input('post.');
            $postParam = $param;
            $param['order_num_data'] = json_decode($param['order_num_data'], true);
            $param['user_id'] = getFrontUserInfo()['id'];

            $orderInfo = $userOrderService->refundTrail($param);
            $orderInfo['data']['refundConf']['only_refund'] =  explode("\n", $orderInfo['data']['refundConf']['only_refund']);
            $orderInfo['data']['refundConf']['goods_refund'] =  explode("\n", $orderInfo['data']['refundConf']['goods_refund']);
            $orderInfo['data']['postParam'] = $postParam;

            View::assign($orderInfo['data']);
        }

        return View::fetch();
    }

    /**
     * 处理申请售后
     */
    public function applyRefund(UserOrderService $userOrderService)
    {
        // 处理提交售后
        if (request()->isPost()) {

            $param = input('post.');
            $userInfo = getFrontUserInfo();
            unset($param['file']);

            if (is_numeric($param['order_detail_id']) && isset($param['refund_num'])) {
                $param['order_num_data'] = json_decode($param['order_num_data'], true);
                $newApplyNumData = [];
                foreach ($param['order_num_data'] as $vo) {
                    $newApplyNumData[] = ['order_detail_id' => $vo['order_detail_id'], 'num' => $param['refund_num']];
                }
                $param['order_num_data'] = json_encode($newApplyNumData);
            }

            if (isset($param['refund_img'])) {
                $param['refund_img'] = implode(',', $param['refund_img']);
            } else {
                $param['refund_img'] = '';
            }

            $res = $userOrderService->doRefundOrder($param, $userInfo);
            return json($res);
        }
    }

    /**
     * 退款进度
     */
    public function refundDetail(OrderRefundService $orderRefundService)
    {
        $res = $orderRefundService->getRefundDetail(input('param.id'), session('home_user_id'));
        if ($res['code'] != 0) {
            return build404($res);
        }
        if (!empty($res['data']['refund_img'])) {
            $res['data']['refund_img'] = explode(',', $res['data']['refund_img']);
        }
        View::assign($res['data']->toArray());

        $vipConf = getConfByType('shop_user_level');
        View::assign([
            'vipConf' => $vipConf
        ]);

        return View::fetch();
    }

    /**
     * 订单售后列表
     */
    public function afterOrder(OrderRefundService $orderRefundService)
    {
        if (request()->isAjax()) {

            return json($orderRefundService->getRefundList(input('param.'), 2));
        }

        return View::fetch();
    }

    /**
     * 取消退款
     */
    public function cancelRefund(UserOrderService $userOrderService)
    {
        if (request()->isAjax()) {

            return json($userOrderService->cancelRefund(input('param.id'), session('home_user_id')));
        }
    }

    /**
     * 物流信息
     */
    public function express(UserOrderService $userOrderService)
    {
        $res = $userOrderService->getExpressInfo(input('param.id'), session('home_user_id'));

        View::assign($res['data']);

        return View::fetch();
    }

    /**
     * 确认收货
     */
    public function received(UserOrderService $userOrderService)
    {
        $res = $userOrderService->doReceived(input('param.id'), session('home_user_id'), session('home_user_name'));
        if ($res['code'] == 0) {
            $res['msg'] = '操作成功';
        }

        return json($res);
    }

    /**
     * 快递信息查询
     */
    public function refundExpress(OrderRefundService $orderRefundService, UserOrderService $userOrderService)
    {
        if (request()->isPost()) {

            $res = $orderRefundService->doRefundExpress(input('post.'), session('home_user_id'));
            if ($res['code'] == 0) {
                $res['msg'] = '操作成功';
            }

            return json($res);
        }

        $res = $userOrderService->getExpressInfo(input('param.id'), session('home_user_id'));
        if ($res['code'] == 0) {
            $res['msg'] = '操作成功';
        }

        return json($res);
    }

    /**
     * 关闭订单
     */
    public function close(UserOrderService $userOrderService)
    {
        $res = $userOrderService->closeOrder(input('param.id'), session('home_user_id'), session('home_user_name'));
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

            $param = input('post.');
            if (isset($param['pictures']) && !empty($param['pictures'])) {
                $param['pictures'] = implode(',', $param['pictures']);
            }

            return json($userOrderService->doAppraise($param, getFrontUserInfo()));
        }

        $orderId = input('param.order_id');
        $orderDetailId = input('param.order_detail_id');

        $res = $userOrderService->getGoodsComments($orderId, $orderDetailId, session('home_user_id'));

        if ($res['code'] != 0) {
            return build404($res);
        }

        if (!empty($res['data']['info']['comment']['pictures'])) {
            $res['data']['info']['comment']['pictures'] = explode(',', $res['data']['info']['comment']['pictures']);
        }

        View::assign($res['data']);

        return View::fetch();
    }
}
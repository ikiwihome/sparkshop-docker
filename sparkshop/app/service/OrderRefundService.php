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

use app\model\order\OrderDetail;
use app\model\order\OrderRefund;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class OrderRefundService
{
    /**
     * 售后订单列表
     * @param array $param
     * @param int $type
     * @return array
     */
    public function getRefundList(array $param, int $type = 1): array
    {
        $userInfo = getUserInfo();
        if ($type == 2) {
            $userInfo = getFrontUserInfo();
        }

        $where[] = ['user_id', '=', $userInfo['id']];
        if (!empty($param['status'])) {
            $where[] = ['status', '=', $param['status']];
        }

        $orderRefundModel = new OrderRefund();
        $list = $orderRefundModel->getPageList($param['limit'], $where)['data'];

        $refundStatus = config('order.refund_order_status');
        $orderDetailModel = new OrderDetail();
        $list->each(function ($item) use ($orderDetailModel, $refundStatus) {
            $data = json_decode($item->apply_refund_data, true);
            $detailIds = [];
            foreach ($data['order_num_data'] as $vo) {
                $detailIds[] = $vo['order_detail_id'];
            }

            $listWhere[] = ['id', 'in', $detailIds];
            $orderDetailList = $orderDetailModel->getAllList($listWhere)['data']->toArray();

            $item->goodsList = $orderDetailList;

            if ($item->refund_type == 2 && $item->step == 3) {
                $item->status_txt = '待退货';
            } else {
                $item->status_txt = $refundStatus[$item->status];
            }

            return $item;
        });

        return dataReturn(0, 'success', $list);
    }

    /**
     * 售后订单详情
     * @param int $refundId
     * @param int $userId
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getRefundDetail(int $refundId, int $userId): array
    {
        // 退款申请详情
        $orderRefundModel = new OrderRefund();
        $info = $orderRefundModel->with(['orderInfo.address'])->where('id', $refundId)->where('user_id', $userId)->find();

        $applyDetailData = json_decode($info['apply_refund_data'], true);
        $detailIds = [];
        $id2Num = [];
        foreach ($applyDetailData['order_num_data'] as $vo) {
            $detailIds[] = $vo['order_detail_id'];
            $id2Num[$vo['order_detail_id']] = $vo['num'];
        }

        $payWay = config('order.pay_way');
        $orderStatus = config('order.pay_status');

        $info['orderInfo']['pay_status'] = $orderStatus[$info['orderInfo']['pay_status']];
        $info['orderInfo']['pay_way'] = $payWay[$info['orderInfo']['pay_way']];

        // 订单详情
        $totalAmount = 0;
        $orderDetailModel = new OrderDetail();
        $detailList = $orderDetailModel->whereIn('id', $detailIds)->select();
        foreach ($detailList as $key => $vo) {
            $detailList[$key]['apply_refund_num'] = $id2Num[$vo['id']];
            $totalAmount = round(($totalAmount + $vo['price'] * $id2Num[$vo['id']]), 2);
        }

        $info['order_detail'] = $detailList;
        $info['order_price'] = $totalAmount;

        // 退货地址
        $info['refund_address'] = getConfByType('shop_refund');

        return dataReturn(0, 'success', $info);
    }

    /**
     * 快递信息查询
     * @param array $param
     * @param int $userId
     * @return array
     */
    public function doRefundExpress(array $param, int $userId): array
    {
        if (empty($param['refund_express_name']) || empty($param['refund_express'])) {
            return dataReturn(-2, '快递名或快递单号不能为空');
        }

        $refundModel = new OrderRefund();
        $refundInfo = $refundModel->findOne([
            'id' => $param['id'],
            'user_id' => $userId
        ])['data'];

        if (empty($refundInfo)) {
            return dataReturn(-1, '订单信息异常');
        }

        $param['step'] = $refundInfo['step'] + 1;
        return $refundModel->updateById($param, $param['id']);
    }
}
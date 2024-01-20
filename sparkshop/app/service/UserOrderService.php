<?php

namespace app\service;

use app\model\order\Order;
use app\model\order\OrderAddress;
use app\model\order\OrderComment;
use app\model\order\OrderDetail;
use app\model\order\OrderExpress;
use app\model\order\OrderRefund;
use app\model\order\OrderStatusChange;
use app\model\user\User;
use app\model\user\UserExperienceLog;
use app\model\user\UserLevel;
use app\traits\UserOrderTrait;
use strategy\express\ExpressProvider;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;

class UserOrderService
{
    use UserOrderTrait;

    /**
     * 订单取消
     * @param int $id
     * @param int $userId
     * @param string $userName
     * @return array
     */
    public function orderCancel(int $id, int $userId, string $userName): array
    {
        Db::startTrans();
        try {

            $orderModel = new Order();
            $orderInfo = $orderModel->where('user_id', $userId)->where('id', $id)
                ->where('status', 2)
                ->where('user_del', 1)
                ->where('is_del', 1)->find();

            if (empty($orderInfo)) {
                Db::rollback();
                return dataReturn(-1, '该订单异常');
            }

            $orderModel->where('id', $id)->update([
                'status' => 7, // 取消
                'refund_status' => 1, // 还可以申请售后
                'pay_price' => 0, // 实际支付为 0
                'cancel_time' => now()
            ]);

            $orderStatusModel = new OrderStatusChange();
            $orderStatusModel->insert([
                'order_id' => $id,
                'original_status' => 2,
                'new_status' => 7,
                'msg' => '订单取消',
                'operator_id' => $userId,
                'operator_name' => $userName,
                'create_time' => now()
            ]);

            // 归还优惠券 TODO V1.1.3改造事件机制
            event('CouponCancel', ['order_id' => $id]);

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return dataReturn(-2, $e->getMessage());
        }

        return dataReturn(0, '取消成功');
    }

    /**
     * 订单内支付
     * @param array $param
     * @param int $userId
     * @return array
     */
    public function goPay(array $param, int $userId): array
    {
        if (empty($param['order_id'])) {
            return dataReturn(-1, '请选择要支付的订单');
        }

        if (empty($param['pay_way'])) {
            return dataReturn(-2, '请选择支付方式');
        }

        $orderModel = new Order();
        $orderInfo = $orderModel->findOne([
            ['id', '=', $param['order_id']],
            ['user_id', '=', $userId]
        ])['data'];

        if (empty($orderInfo)) {
            return dataReturn(-3, '订单数据错误');
        }

        if ($orderInfo['pay_status'] != 1 && $orderInfo['status'] != 2) {
            return dataReturn(-4, '该订单无法支付');
        }

        return $this->dealGoPay($param, $orderInfo, $orderModel, $userId);
    }

    /**
     * 处理订单收货
     * @param int $orderId
     * @param int $userId
     * @param string $userName
     * @return array
     */
    public function doReceived(int $orderId, int $userId, string $userName): array
    {
        $orderModel = new Order();
        $orderInfo = $orderModel->findOne([
            'id' => $orderId,
            'user_id' => $userId
        ])['data'];

        if (empty($orderInfo)) {
            return dataReturn(-1, '订单信息异常');
        }

        if ($orderInfo['status'] != 4) {
            return dataReturn(-2, '该订单尚未发货');
        }

        // 查询是否开启会员等级
        $experience = 0;
        $vip = getConfByType('shop_user_level');
        if ($vip['user_level_open'] == 1) {
            $experience = $this->dealIncUserVip($orderInfo, $vip, $userId);
        }

        $res = $orderModel->updateById([
            'status' => 6,
            'experience' => $experience,
            'received_time' => now(),
            'update_time' => now()
        ], $orderId);
        if ($res['code'] != 0) {
            return $res;
        }

        // 触发确认收货后事件
        event('AfterReceiptOrder', $orderInfo);

        $orderStatusModel = new OrderStatusChange();
        return $orderStatusModel->insertOne([
            'order_id' => $orderId,
            'original_status' => $orderInfo['status'],
            'new_status' => 6,
            'msg' => '订单收货',
            'operator_id' => $userId,
            'operator_name' => $userName,
            'create_time' => now()
        ]);
    }

    /**
     * 获取商品评价信息
     * @param int $orderId
     * @param int $orderDetailId
     * @param int $userId
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getGoodsComments(int $orderId, int $orderDetailId, int $userId): array
    {
        $orderModel = new Order();
        $orderInfo = $orderModel->findOne([
            'id' => $orderId,
            'user_id' => $userId,
            'status' => 6
        ])['data'];

        if (empty($orderInfo)) {
            return dataReturn(-1, '该订单不可评价');
        }

        $orderDetailModel = new OrderDetail();
        $orderDetailInfo = $orderDetailModel->with('comment')->where([
            'id' => $orderDetailId,
            'order_id' => $orderId
        ])->find();

        return dataReturn(0, 'success', [
            'info' => $orderDetailInfo
        ]);
    }

    /**
     * 评价
     * @param array $param
     * @param array $userInfo
     * @return array
     */
    public function doAppraise(array $param, array $userInfo): array
    {
        $userId = $userInfo['id'];
        $userName = $userInfo['name'];
        $userAvatar = $userInfo['avatar'];

        if (isset($param['pictures']) && !empty($param['pictures'])) {
            $refundImg = explode(',', $param['pictures']);
            if (count($refundImg) > 3) {
                return dataReturn(-1, "最多上传3张");
            }
        }

        $orderModel = new Order();
        $orderInfo = $orderModel->findOne([
            'id' => $param['order_id'],
            'user_id' => $userId,
            'status' => 6
        ])['data'];

        if (empty($orderInfo)) {
            return dataReturn(-1, "该订单无法您无法评价");
        }

        if ($orderInfo['user_comments'] == 2) {
            return dataReturn(-2, "该订单您已评价");
        }

        // 订单详情信息
        $orderDetailModel = new OrderDetail();
        $orderDetailInfo = $orderDetailModel->findOne([
            'order_id' => $param['order_id'],
            'id' => $param['order_detail_id']
        ])['data'];

        if (empty($orderDetailInfo)) {
            return dataReturn(-3, "订单异常");
        }

        Db::startTrans();
        try {

            $orderDetailModel->where('id', $orderDetailInfo['id'])->update([
                'user_comments' => 2,
                'user_comments_time' => now()
            ]);

            $orderCommentModel = new OrderComment();
            $orderCommentModel->insert([
                'order_id' => $param['order_id'],
                'order_detail_id' => $param['order_detail_id'],
                'goods_id' => $orderDetailInfo['goods_id'],
                'goods_name' => $orderDetailInfo['goods_name'],
                'type' => $param['type'],
                'desc_match' => $param['desc_match'],
                'sku' => $orderDetailInfo['rule'],
                'content' => $param['content'],
                'pictures' => !empty($param['pictures']) ? $param['pictures'] : '',
                'user_id' => $userId,
                'user_name' => $userName,
                'user_avatar' => $userAvatar,
                'create_time' => now()
            ]);

            $completeComments = $orderDetailModel->where([
                'order_id' => $param['order_id'],
                'user_comments' => 1
            ])->count('id');
            if ($completeComments == 0) {
                $orderModel->where('id', $param['order_id'])->update([
                    'user_comments' => 2,
                    'update_time' => now()
                ]);
            }

            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            return dataReturn(-10, $e->getMessage());
        }

        return dataReturn(0, '评价成功');
    }

    /**
     * 处理用户vip等级
     * @param $orderInfo
     * @param array $vip
     * @param int $userId
     * @return float|mixed
     */
    private function dealIncUserVip($orderInfo, array $vip, int $userId)
    {
        $experience = ceil($orderInfo['pay_price'] * $vip['give_points']);

        // 记录变更日志
        $experienceModel = new UserExperienceLog();
        $experienceModel->insertOne([
            'user_id' => $userId,
            'experience' => $experience,
            'order_id' => $orderInfo['id'],
            'order_code' => $orderInfo['order_no'],
            'remark' => '购物赠送',
            'create_time' => now()
        ]);

        $userModel = new User();
        $userInfo = $userModel->findOne([
            'id' => $userId
        ])['data'];

        // 等级梯度
        $userLevelModel = new UserLevel();
        $userLevelInfo = $userLevelModel->getAllList([], 'level,experience', 'level asc')['data'];

        // 计算此次后的等级
        $nowExperience = $userInfo['experience'] + $experience;
        $level = 0;
        foreach ($userLevelInfo as $vo) {
            if ($nowExperience > $vo['experience']) {
                $level = $vo['level'];
            }
        }

        $userModel = new User();
        if ($level > $userInfo['vip_level']) { // 如果等级增长
            $param = [
                'experience' => $nowExperience,
                'vip_level' => $level
            ];
        } else {
            $param = [
                'experience' => $nowExperience,
            ];
        }

        $userModel->updateById($param, $userId);

        return $nowExperience;
    }

    /**
     * 退款试算
     * @param array $param
     * @return array
     */
    public function refundTrail(array $param): array
    {
        $orderId = $param['order_id'];
        $orderDetailId = $param['order_detail_id']; // 形如: 1,2
        $userId = $param['user_id'];
        $orderNumData = $param['order_num_data']; // 形如：[{order_detail_id: 1, num: 1}, {order_detail_id: 2, num: 3}]
        $orderNumMap = [];
        if (!empty($orderNumData)) {
            foreach ($orderNumData as $vo) {
                $orderNumMap[$vo['order_detail_id']] = $vo['num'];
            }
        }

        $totalNum = 0;
        $orderModel = new Order();
        $orderInfo = $orderModel->findOne([
            ['id', '=', $orderId],
            ['status', 'in', [3, 4, 5, 6]],
            ['user_id', '=', $userId]
        ], 'id,type,pay_status,refund_status,status')['data'];

        if (empty($orderInfo)) {
            return dataReturn(-1, '该订单状态已变更，请稍后再试。');
        }

        if ($orderInfo['refund_status'] == 2) {
            return dataReturn(-11, '该订单退款正在审核中,请勿重复申请');
        }

        $orderDetailModel = new OrderDetail();
        // 订单详情
        $where[] = ['order_id', '=', $orderId];
        if (!empty($orderDetailId)) {
            $where[] = ['id' , 'in', $orderDetailId];
        }

        $orderList = $orderDetailModel->getAllList($where)['data'];
        foreach ($orderList as $key => $vo) {
            if (isset($orderNumMap[$vo['id']])) {
                if ($vo['cart_num'] > $orderNumMap[$vo['id']]) {
                    // 重置数量
                    $orderList[$key]['cart_num'] = $orderNumMap[$vo['id']];
                    $totalNum += $orderNumMap[$vo['id']];
                } else {
                    $totalNum += $vo['cart_num'];
                }
            } else {
                $totalNum += $vo['cart_num'];
            }
        }
        $refundConf = getConfByType('shop_refund');
        if (count($orderList) > 1) {
            $canSelectNum = false;
        } else {
            $canSelectNum = $orderList[0]['cart_num'] > 1;
        }

        // 看是否已过了申请有效期
        if (!empty($orderInfo['received_time'])) {
            $validateDay = getConfByType('shop_refund')['refund_validate_day'] ?? 7;
            if ((time() - strtotime($orderInfo['received_time'])) > $validateDay * 86400) {
                return dataReturn(-4, "抱歉，该订单已过售后申请时效");
            }
        }

        return dataReturn(0, 'success', compact('orderInfo', 'orderList', 'refundConf', 'totalNum', 'canSelectNum'));
    }

    /**
     * 处理退款订单
     * @param array $param
     * @param array $user
     * @return array
     */
    public function doRefundOrder(array $param, array $user): array
    {
        return $this->dealRefundOrder($param, $user['id'], $user['name']);
    }

    /**
     * 取消退款
     * @param int $id
     * @param int $userId
     * @return array
     */
    public function cancelRefund(int $id, int $userId): array
    {
        $refundModel = new OrderRefund();
        $orderInfo = $refundModel->findOne([
            ['id', '=', $id],
            ['user_id', '=', $userId],
            ['status', '=', 1]
        ])['data'];

        if (empty($orderInfo)) {
            return dataReturn(-1, '该订单无法取消退款');
        }

        (new Order())->updateById([
            'refund_status' => 1,
            'update_time' => now()
        ], $orderInfo['order_id']);

        $res = $refundModel->updateById([
            'status' => 4,
            'update_time' => now()
        ], $orderInfo['id']);

        if ($res['code'] == 0) {
            $res['msg'] = '取消成功';
        }
        return $res;
    }

    /**
     * 获取物流信息
     * @param int $id
     * @param int $userId
     * @return array
     */
    public function getExpressInfo(int $id, int $userId): array
    {
        $orderModel = new Order();
        $orderInfo = $orderModel->findOne([
            'id' => $id,
            'user_id' => $userId
        ])['data'];

        $detail = [];
        $addressStr = '';
        if (!empty($orderInfo)) {
            $orderExpressModel = new OrderExpress();
            $expressInfo = $orderExpressModel->findOne([
                'order_id' => $orderInfo['id'],
                'type' => 1 // TODO 退货暂时不提供路由
            ])['data'];

            // 无发货信息
            if (empty($expressInfo)) {
                return dataReturn(0);
            }

            $addressModel = new OrderAddress();
            $addressInfo = $addressModel->findOne(['order_id' => $orderInfo['id']])['data'];
            $addressStr = $addressInfo['province'] . $addressInfo['city'] . $addressInfo['county'] . $addressInfo['detail'];

            // 若查询结束了
            if ($expressInfo['end_flag'] == 1) {
                $detail = json_decode($expressInfo['express'], true);

                return dataReturn(0, 'success', [
                    'express_name' => $orderInfo['delivery_name'],
                    'delivery_no' => $orderInfo['delivery_no'],
                    'detail' => $detail,
                    'address' => $addressStr
                ]);
            }

            $expressProvider = new ExpressProvider('aliyun');
            if (!empty($expressInfo['express'])) {
                $detail = json_decode($expressInfo['express'], true);

                // 2小时查一次
                if ($expressInfo['end_flag'] == 2 && (time() - strtotime($expressInfo['update_time'])) > 7200) {

                    $detail = $expressProvider->getStrategy()->search([
                        'id' => $expressInfo['id'],
                        'order_id' => $orderInfo['id'],
                        'no' => $orderInfo['delivery_no'] . ':' . substr($addressInfo['phone'], 7, 4),
                        'type' => $orderInfo['delivery_code']
                    ])['data'];

                    $detail = json_decode($detail, true);
                }
            } else {

                $detail = $expressProvider->getStrategy()->search([
                    'id' => $expressInfo['id'],
                    'order_id' => $orderInfo['id'],
                    'no' => $orderInfo['delivery_no'] . ':' . substr($addressInfo['phone'], 7, 4),
                    'type' => $orderInfo['delivery_code']
                ])['data'];

                $detail = json_decode($detail, true);
            }

            $endFlag = 2;
            if ($detail['status'] == 0 && isset($detail['result']['deliverystatus'])
                && $detail['result']['deliverystatus'] >= 3) {
                $endFlag = 1;
            }

            $orderExpressModel->updateById([
                'end_flag' => $endFlag,
                'express' => json_encode($detail),
                'update_time' => now()
            ], $expressInfo['id']);
        }

        return dataReturn(0, 'success', [
            'express_name' => $orderInfo['delivery_name'],
            'delivery_no' => $orderInfo['delivery_no'],
            'detail' => $detail,
            'address' => $addressStr
        ]);
    }

    /**
     * 关闭订单
     * @param int $id
     * @param int $userId
     * @param string $userName
     * @return array
     */
    public function closeOrder(int $id, int $userId, string $userName): array
    {
        $orderModel = new Order();
        $orderInfo = $orderModel->findOne([
            'id' => $id,
            'user_id' => $userId,
            'status' => 2
        ])['data'];

        if (empty($orderInfo)) {
            return dataReturn(-1, '订单信息异常');
        }

        $res = $orderModel->updateById([
            'status' => 8,
            'update_time' => now()
        ], $id);
        if ($res['code'] != 0) {
            return $res;
        }

        // 记录状态变更
        $orderStatusModel = new OrderStatusChange();
        return $orderStatusModel->insertOne([
            'order_id' => $id,
            'original_status' => $orderInfo['status'],
            'new_status' => 8,
            'msg' => '订单关闭',
            'operator_id' => $userId,
            'operator_name' => $userName,
            'create_time' => now()
        ]);
    }
}
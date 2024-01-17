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

namespace addons\seckill\subscribe;

use addons\seckill\model\SeckillActivity;
use addons\seckill\model\SeckillOrder;
use addons\seckill\service\OrderService;
use addons\seckill\service\SeckillTimeService;
use app\model\order\OrderDetail;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class SeckillSubscribe
{
    /**
     * 秒杀首页信息
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function onSeckillHomeData() : array
    {
        $seckillTimeService = new SeckillTimeService();
        $timeList = $seckillTimeService->getTimeList()['data'];
        $nowHour = date('H');
        $seckillHour = 0;
        $seckillTimeId = 0;
        $continueHour = 0;
        foreach ($timeList as $vo) {

            $datetimeArr = [];
            for ($i = 0; $i < $vo['continue_hour']; $i++) {
                $datetimeArr[] = $vo['start_hour'] + $i;
            }

            if (in_array($nowHour, $datetimeArr)) {
                $seckillTimeId = $vo['id'];
                $seckillHour = $vo['start_hour'];
                $continueHour = $vo['continue_hour'];
                break;
            }
        }

        $seckillModel = new SeckillActivity();
        $list = $seckillModel->field('id,goods_id,goods_rule,pic,name,original_price,seckill_price,stock,sales')
            ->where('status', 2)->where('is_open', 1)
            ->where('seckill_time_id', $seckillTimeId)
            ->where('start_time', '<', now())
            ->where('end_time', '>', now())
            ->limit(3)->select();

        return dataReturn(0, 'success', compact('list', 'seckillHour', 'continueHour'));
    }

    /**
     * 扣除秒杀库存
     * @param $param
     * @return bool
     */
    public function onSeckillStock($param): bool
    {
        $orderDetail = (new OrderDetail())->findOne([
            'order_id' => $param['id']
        ], 'rule,cart_num')['data'];

        $seckillInfo = (new SeckillOrder())->findOne([
            'order_id' => $param['id']
        ], 'seckill_id')['data'];

        $orderInfo = [
            'seckill_id' => $seckillInfo['seckill_id'],
            'sku' => $orderDetail['rule'],
            'num' => $orderDetail['cart_num']
        ];

        return (new OrderService())->dealStockAndSales($orderInfo);
    }

    /**
     * 返还秒杀库存
     * @param $param
     * @return bool
     */
    public function onRefundSeckillStock($param): bool
    {
        $applyRefundData = json_decode($param['apply_refund_data'], true)['order_num_data'];

        $detailId2Num = [];
        foreach ($applyRefundData as $vo) {
            $detailId2Num[$vo['order_detail_id']] = $vo['num'];
        }

        $orderDetail = (new OrderDetail())->findOne([
            'order_id' => $param['order_id']
        ], 'rule,id')['data'];

        $seckillInfo = (new SeckillOrder())->findOne([
            'order_id' => $param['order_id']
        ], 'seckill_id')['data'];

        $orderInfo = [
            'seckill_id' => $seckillInfo['seckill_id'],
            'sku' => $orderDetail['rule'],
            'num' => $detailId2Num[$orderDetail['id']]
        ];

        return (new OrderService())->refundStockAndSales($orderInfo);
    }
}
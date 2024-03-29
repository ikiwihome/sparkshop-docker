<?php


namespace app\admin\controller;

use app\admin\service\OrderService;
use app\model\order\OrderDetail;
use app\model\order\OrderStatusChange;
use app\model\system\SetExpress;
use think\facade\Db;
use think\facade\View;

class Order extends Base
{
    /**
     * 订单列表
     */
    public function index(OrderService $orderService)
    {
        if (request()->isAjax()) {

            return json($orderService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 发货
     */
    public function express(OrderService $orderService)
    {
        if (request()->isPost()) {

            return json($orderService->doExpress(input('post.')));
        }

        $orderId = input('param.id');
        $setExpressModel = new SetExpress();
        $expressList = $setExpressModel->getAllList([
            'status' => 1
        ], 'name,code', 'id asc')['data'];

        $orderDetail = new OrderDetail();
        $orderInfo = $orderDetail->findOne([
            'order_id' => $orderId
        ], 'goods_id')['data'];

        $goodsModel = new \app\model\goods\Goods();
        $goodsInfo = $goodsModel->findOne([
            'id' => $orderInfo['goods_id']
        ], 'type')['data'];

        return jsonReturn(0, 'success', [
            'express' => $expressList,
            'order_id' => $orderId,
            'goods_type' => $goodsInfo['type']
        ]);
    }

    /**
     * 展示物流信息
     */
    public function showExpress(OrderService $orderService)
    {
        if (request()->isPost()) {

            return json($orderService->showExpress(input('post.')));
        }

        $orderId = input('param.id');
        $setExpressModel = new SetExpress();
        $expressList = $setExpressModel->getAllList([
            'status' => 1
        ], 'name,code', 'id asc')['data'];

        $orderModel = new \app\model\order\Order();
        $info = $orderModel->findOne([
            'id' => $orderId
        ])['data'];

        View::assign([
            'express' => $expressList,
            'order_id' => $orderId,
            'info' => $info
        ]);

        return View::fetch();
    }

    /**
     * 订单详情
     */
    public function detail(OrderService $orderService)
    {
        return json($orderService->showDetail(input('param.id')));
    }

    /**
     * 支付日志
     */
    public function log(OrderStatusChange $orderStatusModel)
    {
        return json($orderStatusModel->getAllList([
            'order_id' => input('param.id')
        ]));
    }

    /**
     * 删除订单
     */
    public function del(\app\model\order\Order $orderModel)
    {
        $orderModel->where('id', input('param.id'))->update([
            'is_del' => 2
        ]);

        return jsonReturn(0, '删除成功');
    }

    /**
     * 统计订单
     */
    public function census()
    {
        $orderParam = [
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0
        ];

        $list = Db::name('order')->fieldRaw('type,count(*) as `total`')->where('pid', '=', 0)->where('status', 3)
            ->where('is_del', 1)->group('type')->select();

        if (!empty($list)) {
            foreach ($list as $vo) {
                $orderParam[$vo['type']] = $vo['total'];
            }
        }

        $orderParam[0] = Db::name('order')->where('pid', '=', 0)->where('status', 3)->where('is_del', 1)->count();
        $orderParam[5] = Db::name('order')->where('is_del', 2)->count();
        return jsonReturn(0, 'success', $orderParam);
    }

    /**
     * 导出订单
     */
    public function export(OrderService $orderService)
    {
        $res = $orderService->dealExport(input('param.'));
        return jsonReturn(0, 'success', $res['data']);
    }

    /**
     * 完成订单
     */
    public function complete(OrderService $orderService)
    {
        return json($orderService->completeOrder(input('param.id')));
    }
}
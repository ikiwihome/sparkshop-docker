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
use app\service\UserService;
use think\facade\View;
use utils\SparkTools;

class User extends Base
{
    public function initialize()
    {
        parent::initialize();
        pcLoginCheck();
    }

    /**
     * 订单列表
     */
    public function index(OrderService $orderService)
    {
        if (request()->isAjax()) {

            return json($orderService->getUserOrderList(input('param.'), session('home_user_id')));
        }

        // 支付方式开启情况
        $payWayConf = SparkTools::getPayWay();

        View::assign([
            'payWayMap' => $payWayConf['payWayMap'],
            'pay_way' => $payWayConf['payWay']
        ]);

        return View::fetch();
    }

    /**
     * 订单详情
     */
    public function detail(OrderService $orderService)
    {
        $res = $orderService->getOrderDetail(input('param.id'), session('home_user_id'));

        if ($res['data']['order']['pid'] == -1) {
            return redirect('/index/user');
        }

        if ($res['code'] != 0) {
            return View::fetch('/404', [
                'error' => $res['msg'],
                'url' => isset(request()->header()['referer']) ? request()->header()['referer'] :  '/'
            ]);
        }

        $res['data']['couponInstalled'] = hasInstalled('coupon');
        View::assign($res['data']);
        $vipConf = getConfByType('shop_user_level');

        View::assign([
            'vipConf' => $vipConf
        ]);

        return View::fetch();
    }

    /**
     * 我的优惠券
     */
    public function coupon()
    {
        return View::fetch();
    }

    /**
     * 个人资料
     */
    public function personal(UserService $userService)
    {
        if (request()->isPost()) {

            $res = $userService->personalData(input('post.'), session('home_user_id'));
            if ($res['code'] != 0) {
                return json($res);
            }

            return jsonReturn(0, "编辑成功");
        }

        $userModel = new \app\model\user\User();
        $info = $userModel->findOne([
            'id' => session('home_user_id')
        ])['data'];

        if ($info['sex'] == 0) {
            $info['sex'] = '未知';
        } else if ($info['sex'] == 1) {
            $info['sex'] = '男';
        } else {
            $info['sex'] = '女';
        }

        View::assign([
            'info' => $info
        ]);

        return View::fetch();
    }

    public function address()
    {
        return View::fetch();
    }

    /**
     * 我的余额
     */
    public function balance()
    {
        $payWayConf = SparkTools::getPayWay();
        View::assign([
            'payWayMap' => $payWayConf['payWayMap'],
            'pay_way' => $payWayConf['payWay'],
        ]);

        return View::fetch();
    }
}
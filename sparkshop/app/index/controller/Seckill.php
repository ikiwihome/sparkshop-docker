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

use addons\seckill\service\OrderService;
use app\model\goods\Goods;
use think\facade\View;

class Seckill extends Base
{
    /**
     * 秒杀首页
     */
    public function index()
    {
        return View::fetch();
    }

    /**
     * 秒杀商品详情
     */
    public function goods(Goods $goodsModel)
    {
        $seckillId = input('param.seckill_id');
        $url = request()->domain() . '/addons/seckill/index.index/detail';
        $seckillInfo = json_decode(file_get_contents($url . '?seckill_id=' . $seckillId), true)['data'];

        // 热卖
        $hotSale = $goodsModel->getLimitList(['is_del' => 2], 4, 'id,name,slider_image,price,sales', 'sales desc')['data'];
        foreach ($hotSale as $key => $vo) {
            $hotSale[$key]['img'] = json_decode($vo['slider_image'], true)[0];
        }

        View::assign($seckillInfo);
        View::assign([
            'seckill_id' => $seckillId,
            'hotSale' => $hotSale
        ]);

        return View::fetch();
    }

    /**
     * 秒杀收银台
     */
    public function goodsInfo(OrderService $orderService)
    {
        $param = input('post.');

        $orderInfo = $orderService->seckillGoodsInfo($param);

        View::assign($orderInfo['data']);
        View::assign([
            'goods' => $param['order_data']
        ]);

        return View::fetch();
    }
}
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

namespace addons\seckill\controller\admin;

use addons\seckill\service\SeckillTimeService;
use app\PluginBaseController;

class SeckillTime extends PluginBaseController
{
    /**
     * 秒杀时间段
     */
    public function index()
    {
        if (request()->isAjax()) {

            $seckillTimeService = new SeckillTimeService();
            $res = $seckillTimeService->getList(input('param.'));
            return json($res);
        }

        return fetch();
    }

    /**
     * 添加时间段
     */
    public function add()
    {
        if (request()->isPost()) {

            $param = input('post.');

            $seckillTimeService = new SeckillTimeService();
            $res = $seckillTimeService->addTime($param);
            return json($res);
        }

        return fetch();
    }

    /**
     * 编辑时间段
     */
    public function edit()
    {
        if (request()->isPost()) {

            $param = input('post.');

            $seckillTimeService = new SeckillTimeService();
            $res = $seckillTimeService->editTime($param);
            return json($res);
        }

        return fetch();
    }

    /**
     * 删除时间段
     */
    public function del()
    {
        if (request()->isAjax()) {

            $id = input('param.id');

            $seckillTimeService = new SeckillTimeService();
            $res = $seckillTimeService->delTime($id);
            return json($res);
        }
    }
}
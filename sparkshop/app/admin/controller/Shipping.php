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

use app\admin\service\ShippingService;
use app\model\system\ShippingTemplates;
use app\model\system\ShippingTemplatesRegion;
use think\facade\View;

class Shipping extends Base
{
    /**
     * 获取列表
     */
    public function index(ShippingService $shippingService)
    {
        if (request()->isAjax()) {

            return json($shippingService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(ShippingService $shippingService)
    {
        if (request()->isPost()) {

            return json($shippingService->addShipping(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(ShippingService $shippingService)
    {
        if (request()->isPost()) {

            return json($shippingService->editShipping(input('post.')));
        }

        if (request()->isAjax()) {

            return json($shippingService->getShippingInfo(input('param.id')));
        }

        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(ShippingTemplates $shippingTemplatesModel)
    {
        $info = $shippingTemplatesModel->updateById([
            'is_del' => 2
        ], input('param.id'));
        $info['msg'] = '删除成功';
        return json($info);
    }
}

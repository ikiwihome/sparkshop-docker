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

use app\admin\service\ExpressService;
use app\model\system\SetExpress;
use think\facade\View;

class Express extends Base
{
    /**
     * 获取列表
     */
    public function index(ExpressService $expressService)
    {
        if (request()->isAjax()) {

            return json($expressService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(ExpressService $expressService)
    {
        if (request()->isPost()) {

            return json($expressService->addExpress(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(ExpressService $expressService, SetExpress $setExpressModel)
    {
        if (request()->isPost()) {

            return json($expressService->editExpress(input('post.')));
        }

        $id = input('param.id');
        View::assign([
            'info' => $setExpressModel->findOne([
                'id' => $id
            ])['data']
        ]);

        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(SetExpress $setExpressModel)
    {
        return json($setExpressModel->delById(input('param.id')));
    }
}

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

use app\admin\service\LabelService;
use app\model\user\UserLabel;
use think\facade\View;

class Label extends Base
{
    /**
     * 获取列表
     */
    public function index(LabelService $labelService)
    {
        if (request()->isAjax()) {

            return json($labelService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(LabelService $labelService)
    {
        if (request()->isPost()) {

            return json($labelService->addLabel(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(LabelService $labelService, UserLabel $labelModel)
    {
        if (request()->isPost()) {

            return json($labelService->editLabel(input('post.')));
        }

        $id = input('param.id');
        View::assign([
            'info' => $labelModel->findOne([
                'id' => $id
            ])['data']
        ]);

        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(UserLabel $labelModel)
    {
        return json($labelModel->delById(input('param.id')));
    }
}

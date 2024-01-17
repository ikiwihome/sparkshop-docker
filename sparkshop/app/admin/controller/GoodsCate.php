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

use app\admin\service\GoodsCateService;
use app\model\goods\GoodsCate as GoodsCateModel;
use think\facade\View;

class GoodsCate extends Base
{
    /**
     * 获取列表
     */
    public function index(GoodsCateModel $goodsCate)
    {
        if (request()->isAjax()) {

            $list = $goodsCate->getAllList([], "*", "id asc");

            return jsonReturn(0, 'success', makeTree($list['data']->toArray()));
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(GoodsCateService $goodsCateService)
    {
        if (request()->isPost()) {

            return json($goodsCateService->addGoodsCate(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(GoodsCateService $goodsCateService, GoodsCateModel $goodsCateModel)
    {
        if (request()->isPost()) {

            return json($goodsCateService->editGoodsCate(input('post.')));
        }

        if (request()->isAjax()) {
            $pid = input('param.pid');
            $id = input('param.id');

            if (0 == $pid) {
                $pName = '顶级分类';
            } else {
                $pName = $goodsCateModel->getInfoById($pid)['data']['name'];
            }

            return jsonReturn(0, 'success', [
                'info' => $goodsCateModel->findOne([
                    'id' => $id
                ])['data'],
                'pName' => $pName,
            ]);
        }


        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(GoodsCateService $goodsCateService)
    {
        return json($goodsCateService->delGoodsCate(input('param.id')));
    }
}

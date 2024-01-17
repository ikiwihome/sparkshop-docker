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

use app\admin\service\GoodsAttrService;
use app\model\goods\GoodsAttrTpl;
use think\facade\View;

class GoodsAttr extends Base
{
    /**
     * 获取列表
     */
    public function index(GoodsAttrService $goodsAttrService)
    {
        if (request()->isAjax()) {

            return json($goodsAttrService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(GoodsAttrService $goodsAttrService)
    {
        if (request()->isPost()) {

            return json($goodsAttrService->addGoodsAttr(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(GoodsAttrService $goodsAttrService, GoodsAttrTpl $goodsAttrTplModel)
    {
        if (request()->isPost()) {

            return json($goodsAttrService->editGoodsAttr(input('post.')));
        }

        $id = input('param.id');
        $info = $goodsAttrTplModel->findOne([
            'id' => $id
        ])['data'];

        View::assign([
            'info' => $info,
            'attr' => json_decode($info['value'], true)
        ]);

        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(GoodsAttrTpl $goodsAttrTplModel)
    {
        return json($goodsAttrTplModel->delById(input('param.id')));
    }
}

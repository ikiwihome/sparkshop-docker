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

use app\admin\service\GoodsRuleService;
use app\model\goods\GoodsRuleTpl as GoodsRuleTplModel;
use think\facade\View;

class GoodsRule extends Base
{
    /**
     * 获取列表
     */
    public function index(GoodsRuleService $goodsRuleService)
    {
        if (request()->isAjax()) {

            return json($goodsRuleService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(GoodsRuleService $goodsRuleService)
    {
        if (request()->isPost()) {

            return json($goodsRuleService->addGoodsRule(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(GoodsRuleService $goodsRuleService, GoodsRuleTplModel $goodsRuleModel)
    {
        if (request()->isPost()) {

            return json($goodsRuleService->editGoodsRule(input('post.')));
        }

        $id = input('param.id');
        $info = $goodsRuleModel->findOne([
            'id' => $id
        ])['data'];

        $first = [];
        $left = [];
        $itemNum = 0;

        if (!empty($info['value'])) {
            $value = json_decode($info['value'], true);
            $first = $value[0];
            unset($value[0]);
            $left = $value;
            $itemNum = count($value);
        }

        View::assign([
            'first' => $first,
            'left' => $left,
            'info' => $info,
            'itemNum' => $itemNum
        ]);

        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(GoodsRuleTplModel $goodsRuleModel)
    {
        return json($goodsRuleModel->delById(input('param.id')));
    }

    /**
     * 获取规格信息
     */
    public function getRuleByGoodsId(GoodsRuleService $goodsRuleService)
    {
        return json($goodsRuleService->getRuleByGoodsId(input('param.goods_id')));
    }
}

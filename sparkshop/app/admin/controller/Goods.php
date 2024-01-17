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

use app\admin\service\GoodsService;
use app\model\goods\Goods as GoodsModel;
use app\model\goods\GoodsContent;
use app\model\goods\GoodsRuleExtend;
use think\facade\View;

class Goods extends Base
{
    /**
     * 获取列表
     */
    public function index(GoodsService $goodsService, \app\model\goods\GoodsCate $goodsCateModel)
    {
        if (request()->isAjax()) {

            $res = $goodsService->getList(input('param.'))['data'];

            $cate = $goodsCateModel->getAllList([
                'status' => 1
            ], 'id,pid,name')['data']->toArray();

            $list['list'] = $res;
            $list['cate'] = makeTree($cate);

            return jsonReturn(0, 'success', $list);
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(GoodsService $goodsService)
    {
        if (request()->isPost()) {

            return json($goodsService->addGoods(input('post.'), 'add'));
        }

        if (request()->isAjax()) {

            return jsonReturn(0, 'success', $goodsService->getBaseParam());
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(GoodsService $goodsService)
    {
        if (request()->isPost()) {

            return json($goodsService->editGoods(input('post.'), 'edit'));
        }

        if (request()->isAjax()) {

            $id = input('param.id');
            $goodsModel = new GoodsModel();
            $goodsRuleModel = new \app\model\goods\GoodsRule();
            $goodsRuleExtendModel = new GoodsRuleExtend();
            $goodsContentModel = new GoodsContent();
            $goodsAttrModel = new \app\model\goods\GoodsAttr();
            $base = [
                'info' => $goodsModel->findOne([
                    'id' => $id
                ])['data'],
                'ruleData' => $goodsRuleModel->findOne([
                    'goods_id' => $id
                ])['data'],
                'extend' => $goodsRuleExtendModel->getAllList([
                    'goods_id' => $id
                ], '*', 'id asc')['data'],
                'content' => $goodsContentModel->findOne([
                    'goods_id' => $id
                ])['data'],
                'attrData' => $goodsAttrModel->getAllList([
                    'goods_id' => $id
                ], '*', 'id asc')['data']
            ];

            $return = array_merge($base, $goodsService->getBaseParam());

            return jsonReturn(0, 'success', $return);
        }

        return View::fetch();
    }

    /**
     * 上下架
     */
    public function shelf(GoodsModel $goodsModel)
    {
        $param = input('post.');

        $res = $goodsModel->updateByIds([
            'is_show' => $param['is_show']
        ], $param['ids']);

        $res['msg'] = '操作成功';
        return json($res);
    }

    /**
     * 删除商品
     */
    public function del(GoodsService $goodsService)
    {
        return json($goodsService->delGoods(input('param.id')));
    }

    public function recover(GoodsModel $goodsModel)
    {
        $res = $goodsModel->updateByIds([
            'is_del' => 2
        ], input('param.id'));

        $res['msg'] = '操作成功';
        return json($res);
    }
}

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

use app\admin\service\AppraiseService;
use app\model\order\OrderComment;
use think\facade\View;

class Appraise extends Base
{
    /**
     * 评价列表
     */
    public function index(AppraiseService $appraiseService)
    {
        if (request()->isAjax()) {

            return json($appraiseService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 删除评价
     */
    public function del(OrderComment $commentModel)
    {
        if (request()->isAjax()) {

            return json($commentModel->delById(input('param.id')));
        }
    }
}
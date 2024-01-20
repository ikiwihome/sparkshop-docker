<?php

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
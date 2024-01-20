<?php

namespace app\api\controller;

use app\BaseController;
use app\service\GoodsCateService;

class GoodsCate extends BaseController
{
    /**
     * 商品分类列表
     */
    public function index(GoodsCateService $goodsCateService)
    {
        return json($goodsCateService->getCateList());
    }
}
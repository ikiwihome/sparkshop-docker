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
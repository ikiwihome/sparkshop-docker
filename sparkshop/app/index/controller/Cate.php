<?php
// +----------------------------------------------------------------------
// | SparkShop 坚持做优秀的商城系统
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2099 http://sparkshop.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai
// +----------------------------------------------------------------------
namespace app\index\controller;

use app\index\service\CateService;
use think\facade\View;

class Cate extends Base
{
    public function index(CateService $cateService)
    {
        $cateId = input('param.id', 0);
        $level = input('param.level', 1);
        $limit = input('param.limit', 20);
        $sortPrice = input('param.p_s', 'asc');
        $search = input('param.search');

        if (!empty($search)) {

            $cateInfo = $cateService->searchGoods($search);
            View::assign($cateInfo['data']);
        } else {

            $cateInfo = $cateService->getCateList($cateId, $level, $limit, $sortPrice);
            View::assign($cateInfo['data']);
        }

        return View::fetch();
    }
}
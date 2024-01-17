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

use app\index\service\HomeService;
use think\facade\View;

class Index extends Base
{
    /**
     * pc商城首页
     */
    public function index(HomeService $homeService)
    {
        $data = $homeService->getHomeData()['data'];
        View::assign($data);

        return View::fetch();
    }

    /**
     * 协议
     */
    public function agreement(HomeService $homeService)
    {
        $type = input('param.type');
        $info = $homeService->getAgreement($type)['data'];

        View::assign([
            'title' => ($type == 1) ? '用户协议' : '隐私协议',
            'info' => $info
        ]);

        return View::fetch();
    }
}
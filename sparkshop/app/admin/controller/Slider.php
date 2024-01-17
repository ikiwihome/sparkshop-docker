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

use app\admin\service\SliderService;
use app\model\system\WebsiteSlider;
use think\facade\View;

class Slider extends Base
{
    /**
     * 获取列表
     */
    public function index(SliderService $sliderService)
    {
        if (request()->isAjax()) {

            return json($sliderService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(SliderService $sliderService)
    {
        if (request()->isPost()) {

            return json($sliderService->addSlider(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(SliderService $sliderService, WebsiteSlider $websiteSliderModel)
    {
        if (request()->isPost()) {

            return json($sliderService->editSlider(input('post.')));
        }

        View::assign([
            'info' => $websiteSliderModel->findOne([
                'id' => input('param.id')
            ])['data']
        ]);

        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(WebsiteSlider $websiteSliderModel)
    {
        return json($websiteSliderModel->delById(input('param.id')));
    }
}


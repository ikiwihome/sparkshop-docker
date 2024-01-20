<?php


namespace app\admin\controller;

use app\admin\service\ComImagesService;
use app\model\system\ComImages as ComImagesModel;
use think\db\exception\DbException;
use think\facade\View;

class ComImages extends Base
{
    /**
     * 获取列表
     */
    public function index(ComImagesService $comImagesService)
    {
        if (request()->isAjax()) {

            return json($comImagesService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(ComImagesService $comImagesService)
    {
        set_time_limit(0);
        return json($comImagesService->delComImages(input('param.ids')));
    }

    /**
     * 移动图片分类
     */
    public function edit(ComImagesModel $comImagesModel)
    {
        $ids = array_unique(input('param.ids'));
        $cateId = input('param.cate_id');

        $res = $comImagesModel->updateByIds([
            'cate_id' => $cateId
        ], $ids);

        return json($res);
    }

    /**
     * 显示图片选择器
     */
    public function show()
    {
        View::assign([
            'type' => input('param.type', 'img'),
            'img_ext' => config('images.ext'),
            'video_ext' => config('images.video_ext'),
            'limit' => input('param.limit'),
            'callback' => input('param.callback')
        ]);

        return View::fetch();
    }
}

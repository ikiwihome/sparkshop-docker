<?php


namespace app\admin\controller;

use app\admin\service\ComImageCateService;
use app\model\system\ComImagesCate;
use think\facade\View;

class ComImageCate extends Base
{
    /**
     * 获取列表
     */
    public function index(ComImagesCate $comImagesCateModel)
    {
        if (request()->isAjax()) {

            $list = $comImagesCateModel->getAllList()['data'];
            return jsonReturn(0, 'success', makeTree($list->toArray()));
        }
    }

    /**
     * 添加
     */
    public function add(ComImageCateService $comImagesCateService)
    {
        if (request()->isPost()) {

            return json($comImagesCateService->addComImageCate(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(ComImageCateService $comImagesCateService, ComImagesCate $comImagesCateModel)
    {
        if (request()->isPost()) {

            return json($comImagesCateService->editComImageCate(input('post.')));
        }

        $id = input('param.id');
        View::assign([
            'info' => $comImagesCateModel->findOne([
                'id' => $id
            ])['data']
        ]);

        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(ComImageCateService $comImagesCateService)
    {
        $id = input('param.id');
        if ($id == 0) {
            return jsonReturn(-3, '不可删除');
        }

        return json($comImagesCateService->delComImageCate($id));
    }
}

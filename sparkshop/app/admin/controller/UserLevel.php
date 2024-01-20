<?php


namespace app\admin\controller;

use app\admin\service\UserLevelService;
use app\model\user\UserLevel as UserLevelModel;
use think\facade\View;

class UserLevel extends Base
{
    /**
     * 获取列表
     */
    public function index(UserLevelService $userLevelService)
    {
        if (request()->isAjax()) {

            return json($userLevelService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(UserLevelService $userLevelService)
    {
        if (request()->isPost()) {

            return json($userLevelService->addUserLevel(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(UserLevelService $userLevelService, UserLevelModel $userLevelModel)
    {
        if (request()->isPost()) {

            return json($userLevelService->editUserLevel(input('post.')));
        }

        View::assign([
            'info' => $userLevelModel->findOne([
                'id' => input('param.id')
            ])['data']
        ]);

        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(UserLevelModel $userLevelModel)
    {
        return json($userLevelModel->delById(input('param.id')));
    }
}

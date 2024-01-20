<?php


namespace app\admin\controller;

use app\admin\service\UserGroupService;
use app\model\user\UserGroup as UserGroupModel;
use think\facade\View;

class UserGroup extends Base
{
    /**
     * 获取列表
     */
    public function index(UserGroupService $userGroupService)
    {
        if (request()->isAjax()) {

            return json($userGroupService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(UserGroupService $userGroupService)
    {
        if (request()->isPost()) {

            return json($userGroupService->addUserGroup(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(UserGroupService $userGroupService, UserGroupModel $userGroupModel)
    {
        if (request()->isPost()) {

            return json($userGroupService->editUserGroup(input('post.')));
        }

        $id = input('param.id');
        View::assign([
            'info' => $userGroupModel->findOne([
                'id' => $id
            ])['data']
        ]);

        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(UserGroupModel $userGroupModel)
    {
        return json($userGroupModel->delById(input('param.id')));
    }
}

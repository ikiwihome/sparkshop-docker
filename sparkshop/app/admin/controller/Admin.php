<?php


namespace app\admin\controller;

use app\admin\service\AdminService;
use app\model\system\AdminRole;
use app\model\system\AdminUser;
use think\facade\View;

class Admin extends Base
{
    /**
     * 管理员列表
     */
    public function index(AdminService $adminService)
    {
        if (request()->isAjax()) {

            return json($adminService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 添加管理员
     */
    public function add(AdminService $adminService, AdminRole $adminRoleModel)
    {
        if (request()->isPost()) {

            return json($adminService->addAdmin(input('post.')));
        }

        $where[] = ['status', '=', 1];
        $where[] = ['id', '>', 1];
        return json($adminRoleModel->getAllList($where));
    }

    /**
     * 编辑管理员
     */
    public function edit(AdminService $adminService, AdminRole $adminRoleModel)
    {
        if (request()->isPost()) {

            return json($adminService->editAdmin(input('post.')));
        }

        $where[] = ['status', '=', 1];
        $where[] = ['id', '>', 1];
        return json($adminRoleModel->getAllList($where));
    }

    /**
     * 删除管理员
     */
    public function del(AdminUser $adminUserModel)
    {
        $id = input('param.id');

        if ($id === 1) {
            return jsonReturn(-1, '超级管理员不可以删除');
        }

        return json($adminUserModel->delById($id));
    }
}
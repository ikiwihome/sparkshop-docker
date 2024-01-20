<?php


namespace app\admin\controller;

use app\admin\service\MenuService;
use app\admin\service\RoleService;
use think\facade\View;

class Role extends Base
{
    /**
     * 获取列表
     */
    public function index(RoleService $roleService)
    {
        if (request()->isAjax()) {

            return json($roleService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(RoleService $roleService, MenuService $menuService)
    {
        if (request()->isPost()) {

            return json($roleService->addRole(input('post.')));
        }

        return jsonReturn(0, 'success', $menuService->getNodeTree());
    }

    /**
     * 编辑
     */
    public function edit(RoleService $roleService, MenuService $menuService)
    {
        if (request()->isPost()) {

            return json($roleService->editRole(input('post.')));
        }

        return jsonReturn(0, 'success', $menuService->getNodeTree());
    }

    /**
     * 删除
     */
    public function del(RoleService $roleService)
    {
        return json($roleService->delRole(input('param.id')));
    }
}

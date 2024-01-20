<?php


namespace app\admin\controller;

use app\admin\service\MenuService;
use think\facade\View;

class Menu extends Base
{
    /**
     * 菜单列表
     */
    public function index(MenuService $menuService)
    {
        if (request()->isAjax()) {

            return json($menuService->getMenuTree());
        }

        return View::fetch();
    }

    /**
     * 新增菜单
     */
    public function add(MenuService $menuService)
    {
        if (request()->isPost()) {

            return json($menuService->addMenu(input('post.')));
        }
    }

    /**
     * 编辑菜单
     */
    public function edit(MenuService $menuService)
    {
        if (request()->isPost()) {

            return json($menuService->editMenu(input('post.')));
        }
    }

    /**
     * 删除菜单
     */
    public function del(MenuService $menuService)
    {
        return json($menuService->delMenu(input('param.id')));
    }
}
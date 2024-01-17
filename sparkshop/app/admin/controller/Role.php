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

<?php


namespace app\admin\service;

use app\admin\validate\AdminNodeValidate;
use app\model\system\AdminNode;
use think\exception\ValidateException;

class MenuService
{
    /**
     * 获取菜单树
     * @return array
     */
    public function getMenuTree(): array
    {
        $adminNodeModel = new AdminNode();
        $list = $adminNodeModel->getAllList(['status' => 1], '*', 'sort desc')['data'];

        return dataReturn(0, 'success', makeTree($list->toArray()));
    }

    /**
     * 添加菜单
     * @param array $param
     * @return array
     */
    public function addMenu(array $param): array
    {
        try {
            validate(AdminNodeValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $adminNodeModel = new AdminNode();
        // 检查唯一
        $has = $adminNodeModel->checkUnique([
            ['path', '=', $param['path']],
            ['path', '<>', '#'],
            ['status', '=', 1]
        ])['data'];
        if (!empty($has)) {
            return dataReturn(-2, '该菜单路由已经存在');
        }

        $param['create_time'] = now();
        return $adminNodeModel->insertOne($param);
    }

    /**
     * 编辑菜单
     * @param array $param
     * @return array
     */
    public function editMenu(array $param): array
    {
        try {
            validate(AdminNodeValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $adminNodeModel = new AdminNode();
        // 检查唯一
        $has = $adminNodeModel->checkUnique([
            ['path', '=', $param['path']],
            ['id', '<>', $param['id']],
            ['path', '<>', '#'],
            ['status', '=', 1]
        ])['data'];
        if (!empty($has)) {
            return dataReturn(-2, '该菜单路由已经存在');
        }

        $param['update_time'] = now();
        return $adminNodeModel->updateById($param, $param['id']);
    }

    /**
     * 删除带单
     * @param int $id
     * @return array
     */
    public function delMenu(int $id): array
    {
        $adminNodeModel = new AdminNode();

        $has = $adminNodeModel->findOne(['pid' => $id])['data'];
        if (!empty($has)) {
            return dataReturn(-1, '该菜单下有子菜单，不可删除');
        }

        return $adminNodeModel->delById($id);
    }

    /**
     * 获取菜单节点树
     * @return array
     */
    public function getNodeTree(): array
    {
        $adminNodeModel = new AdminNode();
        $nodeList = $adminNodeModel->getAllList(['status' => 1], 'id,name,pid', 'sort desc')['data'];
        return makeTree($nodeList->toArray());
    }

    /**
     * 获取超管的节点
     * @return array
     */
    public function getSuperAdminNode(): array
    {
        $adminNodeModel = new AdminNode();
        return $adminNodeModel->getAllList([
            'status' => 1,
            'is_menu' => 2
        ], 'id,name,path,pid,icon', 'sort desc')['data']->toArray();
    }
}
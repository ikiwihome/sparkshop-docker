<?php

namespace app\admin\service;

use app\admin\validate\AdminRoleValidate;
use app\model\system\AdminRole;
use app\model\system\AdminUser;
use think\db\exception\DbException;
use think\exception\ValidateException;

class RoleService
{
    /**
     * 获取角色列表
     * @param array $param
     * @return array
     * @throws DbException
     */
    public function getList(array $param): array
    {
        $limit = $param['limit'];
        $name = $param['name'];

        $where = [];
        if (!empty($name)) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }

        $adminRoleModel = new AdminRole();
        $list = $adminRoleModel->where($where)->order('id desc')->paginate($limit);

        return dataReturn(0, 'success', $list);
    }

    /**
     * 添加角色
     * @param array $param
     * @return array
     */
    public function addRole(array $param): array
    {
        // 检验完整性
        try {
            validate(AdminRoleValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $adminRoleModel = new AdminRole();
        $has = $adminRoleModel->checkUnique([
            'name' => $param['name']
        ])['data'];

        if (!empty($has)) {
            return dataReturn(-2, '该角色已经存在');
        }

        $param['create_time'] = now();
        return $adminRoleModel->insertOne($param);
    }

    /**
     * 编辑角色
     * @param array $param
     * @return array
     */
    public function editRole(array $param): array
    {
        // 检验完整性
        try {
            validate(AdminRoleValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $adminRoleModel = new AdminRole();

        $where[] = ['id', '<>', $param['id']];
        $where[] = ['name', '=', $param['name']];
        $has = $adminRoleModel->checkUnique($where)['data'];

        if (!empty($has)) {
            return dataReturn(-2, '该角色已经存在');
        }

        return $adminRoleModel->updateById($param, $param['id']);
    }

    /**
     * 删除角色
     * @param int $id
     * @return array
     */
    public function delRole(int $id): array
    {
        $adminUserModel = new AdminUser();
        $has = $adminUserModel->findOne([
            'role_id' => $id
        ], 'id')['data'];
        if (!empty($has)) {
            return dataReturn(-1, '该角色已被使用无法删除');
        }

        $adminRoleModel = new AdminRole();
        return $adminRoleModel->delById($id);
    }
}
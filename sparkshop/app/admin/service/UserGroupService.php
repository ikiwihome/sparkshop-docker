<?php

namespace app\admin\service;

use app\admin\validate\UserGroupValidate;
use app\model\user\UserGroup;
use think\db\exception\DbException;
use think\exception\ValidateException;

class UserGroupService
{
    /**
     * 获取用户分组
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

        $userGroupModel = new UserGroup();
        $list = $userGroupModel->where($where)->order('id desc')->paginate($limit);

        return dataReturn(0, 'success', $list);
    }

    /**
     * 添加用户分组
     * @param array $param
     * @return array
     */
    public function addUserGroup(array $param): array
    {
        // 检验完整性
        try {
            validate(UserGroupValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $userGroupModel = new UserGroup();
        $has = $userGroupModel->checkUnique([
            'name' => $param['name']
        ])['data'];

        if (!empty($has)) {
            return dataReturn(-2, '该分组已经存在');
        }

        $param['create_time'] = now();
        return $userGroupModel->insertOne($param);
    }

    /**
     * 编辑用户分组
     * @param array $param
     * @return array
     */
    public function editUserGroup(array $param): array
    {
        // 检验完整性
        try {
            validate(UserGroupValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $userGroupModel = new UserGroup();

        $where[] = ['id', '<>', $param['id']];
        $where[] = ['name', '=', $param['name']];
        $has = $userGroupModel->checkUnique($where)['data'];

        if (!empty($has)) {
            return dataReturn(-2, '该分组已经存在');
        }
        return $userGroupModel->updateById($param, $param['id']);
    }
}
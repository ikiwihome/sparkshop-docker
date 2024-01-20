<?php

namespace app\admin\service;

use app\admin\validate\UserLevelValidate;
use app\model\user\UserLevel;
use think\db\exception\DbException;
use think\exception\ValidateException;

class UserLevelService
{
    /**
     * 用户等级列表
     * @param array $param
     * @return array
     * @throws DbException
     */
    public function getList(array $param): array
    {
        $limit = $param['limit'];
        $title = $param['title'];
        $where = [];

        if (!empty($title)) {
            $where[] = ['title', 'like', '%' . $title . '%'];
        }

        $userLevelModel = new UserLevel();
        $list = $userLevelModel->where($where)->order('level asc')->paginate($limit);

        return dataReturn(0, 'success', $list);
    }

    /**
     * 增加用户等级
     * @param array $param
     * @return array
     */
    public function addUserLevel(array $param): array
    {
        if (isset($param['file'])) {
            unset($param['file']);
        }

        // 检验完整性
        try {
            validate(UserLevelValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $userLevelModel = new UserLevel();
        $has = $userLevelModel->checkUnique([
            'level' => $param['level']
        ])['data'];

        if (!empty($has)) {
            return dataReturn(-2, '该等级已经存在');
        }

        $param['create_time'] = now();
        return $userLevelModel->insertOne($param);
    }

    /**
     * 编辑用户等级
     * @param array $param
     * @return array
     */
    public function editUserLevel(array $param): array
    {
        if (isset($param['file'])) {
            unset($param['file']);
        }

        // 检验完整性
        try {
            validate(UserLevelValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $userLevelModel = new UserLevel();

        $where[] = ['id', '<>', $param['id']];
        $where[] = ['level', '=', $param['level']];
        $has = $userLevelModel->checkUnique($where)['data'];

        if (!empty($has)) {
            return dataReturn(-2, '该等级已经存在');
        }

        return $userLevelModel->updateById($param, $param['id']);
    }
}
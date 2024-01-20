<?php

namespace app\admin\service;

use app\admin\validate\SetExpressValidate;
use app\model\system\SetExpress;
use think\db\exception\DbException;
use think\exception\ValidateException;

class ExpressService
{
    /**
     * 获取快递列表
     * @param $param
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

        $setExpressModel = new SetExpress();
        $list = $setExpressModel->where($where)->order('id desc')->paginate($limit);
        return dataReturn(0, 'success', $list);
    }

    /**
     * 添加快递
     * @param array $param
     * @return array
     */
    public function addExpress(array $param): array
    {
        // 检验完整性
        try {
            validate(SetExpressValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $setExpressModel = new SetExpress();
        $has = $setExpressModel->checkUnique([
            'name' => $param['name']
        ])['data'];

        if (!empty($has)) {
            return dataReturn(-2, '物流公司名称已经存在');
        }

        $param['create_time'] = now();
        return $setExpressModel->insertOne($param);
    }

    /**
     * 编辑快递
     * @param array $param
     * @return array
     */
    public function editExpress(array $param): array
    {
        // 检验完整性
        try {
            validate(SetExpressValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $setExpressModel = new SetExpress();

        $where[] = ['id', '<>', $param['id']];
        $where[] = ['name', '=', $param['name']];
        $has = $setExpressModel->checkUnique($where)['data'];

        if (!empty($has)) {
            return dataReturn(-2, '物流公司名称已经存在');
        }

        return $setExpressModel->updateById($param, $param['id']);
    }
}
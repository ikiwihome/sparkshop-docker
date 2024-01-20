<?php

namespace app\admin\service;

use app\admin\validate\SetCityValidate;
use app\model\system\SetCity;
use think\exception\ValidateException;
use think\response\Json;

class CityService
{
    /**
     * 获取城市列表
     * @param array $where
     * @param int $level
     * @return array
     */
    public function getList(array $where, int $level): array
    {
        $cityModel = new SetCity();
        if (request()->isAjax()) {

            $list = $cityModel->getAllList($where, "id,pid,name,level,is_show", "id asc")['data'];
            foreach ($list as $key => $vo) {
                if ($level <= 1) {
                    $list[$key]['hasChildren'] = true;
                    $list[$key]['children'] = [];
                }
            }

            return dataReturn(0, 'success', $list);
        }

        $list = $cityModel->getAllList($where, "id,pid,name,level,is_show", "id asc")['data'];
        foreach ($list as $key => $vo) {
            $list[$key]['hasChildren'] = true;
            $list[$key]['children'] = [];
        }

        return dataReturn(0, 'success', $list);
    }

    /**
     * 添加城市
     * @param array $param
     * @return array
     */
    public function addCity(array $param): array
    {
        // 检验完整性
        try {
            validate(SetCityValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $setCityModel = new SetCity();
        $has = $setCityModel->checkUnique([
            'name' => $param['name'],
            'pid' => $param['pid'],
        ])['data'];

        if (!empty($has)) {
            return dataReturn(-2, '名称已经存在');
        }

        return $setCityModel->insertOne($param);
    }

    /**
     * 编辑城市
     * @param array $param
     * @return array
     */
    public function editCity(array $param): array
    {
        // 检验完整性
        try {
            validate(SetCityValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $setCityModel = new SetCity();

        $where[] = ['id', '<>', $param['id']];
        $where[] = ['name', '=', $param['name']];
        $has = $setCityModel->checkUnique($where)['data'];

        if (!empty($has)) {
            return dataReturn(-2, '名称已经存在');
        }

        return $setCityModel->updateById($param, $param['id']);
    }

    /**
     * 地区tree
     * @param int $level
     * @return array
     */
    public function getAreaTree(int $level = 3): array
    {
        $cityModel = new SetCity();
        $where[] = ['level', '<=', $level];
        $list = $cityModel->getAllList($where, "id,pid,name,level,is_show", "id asc")['data'];

        $tree = makeTree($list->toArray());

        return dataReturn(0, 'success', $tree);
    }
}
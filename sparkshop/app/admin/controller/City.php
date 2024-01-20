<?php


namespace app\admin\controller;

use app\admin\service\CityService;
use app\model\system\SetCity;
use think\facade\View;

class City extends Base
{
    /**
     * 获取列表
     */
    public function index(CityService $cityService)
    {
        if (request()->isAjax()) {
            $pid = input('param.pid', 0);
            $level = input('param.level', 0);
            $where[] = ['pid', '=', $pid];

            $res = $cityService->getList($where, $level);

            return jsonReturn(0, 'success', $res['data']);
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(CityService $cityService)
    {
        if (request()->isPost()) {

            return json($cityService->addCity(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(CityService $cityService, SetCity $setCityModel)
    {
        if (request()->isPost()) {

            return json($cityService->editCity(input('post.')));
        }

        $id = input('param.id');
        View::assign([
            'info' => $setCityModel->findOne([
                'id' => $id
            ])['data']
        ]);

        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(SetCity $setCityModel)
    {
        $id = input('param.id');
        $has = $setCityModel->where('pid', $id)->find();
        if (!empty($has)) {
            return jsonReturn(-1, '该地区下还存在地区，不可删除');
        }

        $info = $setCityModel->delById($id);

        return json($info);
    }

    /**
     * 获取所有的区域
     */
    public function area(CityService $cityService)
    {
        return json($cityService->getAreaTree(input('param.level', 3)));
    }
}

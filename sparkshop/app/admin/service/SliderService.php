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
namespace app\admin\service;

use app\admin\validate\WebsiteSliderValidate;
use app\model\system\WebsiteSlider;
use think\db\exception\DbException;
use think\exception\ValidateException;

class SliderService
{
    /**
     * 获取轮播列表
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

        $websiteSliderModel = new WebsiteSlider();
        $list = $websiteSliderModel->where($where)->order('sort desc')->paginate($limit);

        return dataReturn(0, 'success', $list);
    }

    /**
     * 添加轮播图
     * @param array $param
     * @return array
     */
    public function addSlider(array $param): array
    {
        // 检验完整性
        try {
            validate(WebsiteSliderValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $websiteSliderModel = new WebsiteSlider();
        $has = $websiteSliderModel->checkUnique([
            'name' => $param['name']
        ])['data'];

        if (!empty($has)) {
            return dataReturn(-2, '轮播描述已经存在');
        }

        $param['create_time'] = now();
        return $websiteSliderModel->insertOne($param);
    }

    /**
     * 编辑轮播图
     * @param array $param
     * @return array
     */
    public function editSlider(array $param): array
    {
        // 检验完整性
        try {
            validate(WebsiteSliderValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $websiteSliderModel = new WebsiteSlider();

        $where[] = ['id', '<>', $param['id']];
        $where[] = ['name', '=', $param['name']];
        $has = $websiteSliderModel->checkUnique($where)['data'];

        if (!empty($has)) {
            return dataReturn(-2, '轮播描述已经存在');
        }

        $param['update_time'] = now();
        return $websiteSliderModel->updateById($param, $param['id']);
    }
}
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

use app\admin\validate\ArticleCateValidate;
use app\model\system\ArticleCate;
use think\db\exception\DbException;
use think\exception\ValidateException;
use think\response\Json;

class ArticleCateService
{
    /**
     * 获取文章分类列表
     * @param array $param
     * @return array
     * @throws DbException
     */
    public function getList(array $param): array
    {
        $name = $param['name'];
        $limit = $param['limit'];

        $where = [];
        if (!empty($name)) {
            $where[] = ['name', 'like', '%' . $name . '%'];
        }

        $articleCateModel = new ArticleCate();
        $list = $articleCateModel->where($where)->order('id desc')->paginate($limit);

        return dataReturn(0, 'success', $list);
    }

    /**
     * 添加文章分类
     * @param array $param
     * @return array
     */
    public function addArticleCate(array $param): array
    {
        // 检验完整性
        try {
            validate(ArticleCateValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $articleCateModel = new ArticleCate();
        $has = $articleCateModel->checkUnique([
            'name' => $param['name']
        ])['data'];

        if (!empty($has)) {
            return dataReturn(-2, '分类名已经存在');
        }

        $param['create_time'] = now();
        return $articleCateModel->insertOne($param);
    }

    /**
     * 编辑文章
     * @param array $param
     * @return array
     */
    public function editArticle(array $param): array
    {
        // 检验完整性
        try {
            validate(ArticleCateValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $articleCateModel = new ArticleCate();

        $where[] = ['id', '<>', $param['id']];
        $where[] = ['name', '=', $param['name']];
        $has = $articleCateModel->checkUnique($where)['data'];

        if (!empty($has)) {
            return dataReturn(-2, '分类名已经存在');
        }

        return $articleCateModel->updateById($param, $param['id']);
    }
}
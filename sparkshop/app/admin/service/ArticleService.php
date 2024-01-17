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

use app\admin\validate\ArticleValidate;
use app\model\system\Article;
use think\db\exception\DbException;
use think\exception\ValidateException;
use think\response\Json;

class ArticleService
{
    /**
     * 获取文章列表
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

        $articleModel = new Article();
        $list = $articleModel->with('cateInfo')->where($where)->order('id desc')->paginate($limit);

        return dataReturn(0, 'success', $list);
    }

    /**
     * 添加文章
     * @param array $param
     * @return array
     */
    public function addArticle(array $param): array
    {
        // 检验完整性
        try {
            validate(ArticleValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $articleModel = new Article();
        $has = $articleModel->checkUnique([
            'title' => $param['title']
        ])['data'];

        if (!empty($has)) {
            return dataReturn(-2, '文章标题已经存在');
        }

        $param['create_time'] = now();
        return $articleModel->insertOne($param);
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
            validate(ArticleValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $articleModel = new Article();

        $where[] = ['id', '<>', $param['id']];
        $where[] = ['title', '=', $param['title']];
        $has = $articleModel->checkUnique($where)['data'];

        if (!empty($has)) {
            return dataReturn(-2, '文章标题已经存在');
        }

        return $articleModel->updateById($param, $param['id']);
    }
}
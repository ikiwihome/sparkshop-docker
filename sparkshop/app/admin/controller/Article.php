<?php


namespace app\admin\controller;

use app\admin\service\ArticleService;
use app\model\system\Article as ArticleModel;
use think\facade\View;

class Article extends Base
{
    /**
     * 获取列表
     */
    public function index(ArticleService $articleService)
    {
        if (request()->isAjax()) {

            return json($articleService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(ArticleService $articleService, \app\model\system\ArticleCate $articleCateModel)
    {
        if (request()->isPost()) {

            return json($articleService->addArticle(input('post.')));
        }

        if (request()->isAjax()) {
            $res = $articleCateModel->getAllList([
                'status' => 1
            ], 'id,name');

            return json($res);
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(ArticleService $articleService, \app\model\system\ArticleCate $articleCateModel, ArticleModel $articleModel)
    {
        if (request()->isPost()) {

            return json($articleService->editArticle(input('post.')));
        }

        if (request()->isAjax()) {

            $id = input('param.id');

            return jsonReturn(0, 'SUCCESS', [
                'info' => $articleModel->findOne([
                    'id' => $id
                ])['data'],
                'cate' => $articleCateModel->getAllList([
                    'status' => 1
                ], 'id,name')['data']
            ]);
        }

        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(ArticleModel $articleModel)
    {
        return json($articleModel->delById(input('param.id')));
    }
}


<?php


namespace app\admin\controller;

use app\admin\service\ArticleCateService;
use app\model\system\ArticleCate as ArticleCateModel;
use think\facade\View;

class ArticleCate extends Base
{
    /**
     * 获取列表
     */
    public function index(ArticleCateService $articleCateService)
    {
        if (request()->isAjax()) {

            return json($articleCateService->getList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 添加
     */
    public function add(ArticleCateService $articleCateService)
    {
        if (request()->isPost()) {

            return json($articleCateService->addArticleCate(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 编辑
     */
    public function edit(ArticleCateService $articleCateService, ArticleCateModel $articleCateModel)
    {
        if (request()->isPost()) {

            return json($articleCateService->editArticle(input('post.')));
        }

        $id = input('param.id');
        View::assign([
            'info' => $articleCateModel->findOne([
                'id' => $id
            ])['data']
        ]);

        return View::fetch();
    }

    /**
     * 删除
     */
    public function del(\app\model\system\Article $articleModel, ArticleCateModel $articleCateModel)
    {
        $id = input('param.id');

        $has = $articleModel->field('id')->where('cate_id', $id)->find();
        if (!empty($has)) {
            return jsonReturn(-1, "该分类下有文章不可删除");
        }

        return json($articleCateModel->delById($id));
    }
}


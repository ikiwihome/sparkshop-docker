<?php

namespace app\api\controller;

use app\BaseController;
use app\service\ArticleService;

class Article extends BaseController
{
    /**
     * 获取文章详情
     */
    public function detail(ArticleService $articleService)
    {
        return json($articleService->getArticleInfo(input('param.id')));
    }
}
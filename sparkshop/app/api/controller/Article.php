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
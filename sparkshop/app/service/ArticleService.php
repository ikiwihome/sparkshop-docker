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
namespace app\service;

use app\model\system\Article;

class ArticleService
{
    /**
     * 获取文章详情
     * @param int $id
     * @return array
     */
    public function getArticleInfo(int $id) : array
    {
        $articleModel = new Article();
        $info = $articleModel->findOne(['id' => $id])['data'];
        if (!empty($info)) {
            $info['create_time'] = date('m-d H:i', strtotime($info['create_time']));
        }

        $articleModel->where('id', $id)->inc('views')->update();

        return dataReturn(0, 'success', $info);
    }
}
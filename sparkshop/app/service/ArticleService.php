<?php

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
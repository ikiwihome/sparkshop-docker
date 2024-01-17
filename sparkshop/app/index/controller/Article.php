<?php
// +----------------------------------------------------------------------
// | SparkShop 坚持做优秀的商城系统
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2099 http://sparkshop.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai
// +----------------------------------------------------------------------
namespace app\index\controller;

use think\facade\View;

class Article extends Base
{
    // 文章内容
    public function detail(\app\model\system\Article $article)
    {
        $id = input('param.id');

        $info = $article->findOne(['id' => $id])['data'];
        View::assign([
            'info' => $info
        ]);

        return View::fetch();
    }
}
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

namespace addons\<<pluginName>>\controller\<<moduleName>>;

use app\PluginBaseController;

class Index extends PluginBaseController
{
    public function index()
    {
        if (request()->isAjax()) {

            $param = input('param.');

            return jsonReturn(0, '请求成功');
        }

        return fetch();
    }
}
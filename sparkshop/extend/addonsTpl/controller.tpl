<?php


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
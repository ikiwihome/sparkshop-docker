<?php


namespace addons\<<pluginName>>\controller\<<moduleName>>;

use app\api\controller\Base;

class Index extends Base
{
    public function index()
    {
        return jsonReturn(0, '访问成功');
    }
}
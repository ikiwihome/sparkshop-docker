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

namespace addons\diy\controller\admin;

use app\model\system\SysSetting;
use app\PluginBaseController;

class Diy extends PluginBaseController
{
    public function index()
    {
        return fetch();
    }

    /**
     * 设置diy数据
     */
    public function edit()
    {
        $settingModel = new SysSetting();
        if (request()->isPost()) {

            $param = input('post.data');
            $bg = input('post.bg');

            $settingModel->updateByWhere(['value' => json_encode($bg)], ['key' => 'miniapp_home_bg_diy']);
            return $settingModel->updateByWhere(['value' => json_encode($param)], ['key' => 'miniapp_home_diy']);
        }

        if (request()->isAjax()) {
            $data = getConfByType('diy');
            return jsonReturn(0, 'success', $data);
        }

        return fetch();
    }

    public function preview()
    {
        return fetch();
    }
}
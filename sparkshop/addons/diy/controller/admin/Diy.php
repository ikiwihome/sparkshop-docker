<?php


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
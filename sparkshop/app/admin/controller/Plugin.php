<?php


namespace app\admin\controller;

use app\admin\service\PluginService;
use app\BaseController;
use think\facade\View;

class Plugin extends BaseController
{
    public function initialize()
    {
        $config = config('shop');

        View::assign([
            'title' => $config['title'],
            'is_debug' => $config['is_open_debug']
        ]);
    }

    /**
     * 应用中心
     */
    public function index(PluginService $pluginService)
    {
        if (request()->isAjax()) {

            return json($pluginService->getAppList(input('param.')));
        }

        return View::fetch();
    }

    /**
     * 创建应用
     */
    public function create(PluginService $pluginService)
    {
        if (request()->isPost()) {

            return json($pluginService->createPlugin(input('post.')));
        }
    }

    /**
     * 卸载插件
     */
    public function uninstall(PluginService $pluginService)
    {
        return json($pluginService->uninstallPlugin(input('param.id')));
    }

    /**
     * 安装插件
     */
    public function install(PluginService $pluginService)
    {
        return json($pluginService->installPlugin(input('param.id')));
    }

    /**
     * 删除插件
     */
    public function del(PluginService $pluginService)
    {
        return json($pluginService->deletePlugin(input('param.id')));
    }

    /**
     * 上传插件
     */
    public function import(PluginService $pluginService)
    {
        return json($pluginService->updateOrInstall(input('post.')));
    }

    /**
     * 打包
     */
    public function pack(PluginService $pluginService)
    {
        return json($pluginService->doPack(input('param.name')));
    }

    /**
     * 制作升级包
     */
    public function update(PluginService $pluginService)
    {
        return json($pluginService->makeUpdateZip(input('post.')));
    }
}
<?php


namespace app;

use think\facade\App;
use think\session\driver\File;

class PluginBaseController extends BaseController
{
    public $admin;

    public function initialize()
    {
        $this->initAdmin();
        if (empty($this->admin['admin_id'])) {
            if (request()->isAjax()) {
                exit(json_encode(dataReturn(401, "请先登录")));
            }

            header('location:' . url('login/index'));
            exit;
        }

        if ($this->admin['admin_id'] != 1) {
            $this->checkAuth();
        }
    }

    /**
     * 权限检测
     */
    protected function checkAuth()
    {
        $checkAuth = request()->pathinfo();
        $config = $this->getConfig();

        $authMap = json_decode($this->admin['auth_node'], true);
        $skipAuth = $config['skip_auth'];

        if (!isset($skipAuth[$checkAuth]) && !isset($authMap[$checkAuth])) {

            if (request()->isAjax()) {
                exit(json_encode(['code' => 403, 'data' => [], 'msg' => '您没有权限']));
            }

            exit(file_get_contents(app()->getAppPath() . 'view/default/error_tpl.html'));
        }
    }

    /**
     * 初始化admin信息
     * @return bool
     */
    private function initAdmin()
    {
        // TODO redis方式的存储，后面在做
        if (request()->isAjax()) {
            $sessionConfig = include App()->getAppPath() . config('shop.backend_index') . DS . 'config' . DS . 'session.php';
        } else {
            $sessionConfig = include App()->getAppPath() . 'config' . DS . 'session.php';
        }

        if ($sessionConfig['type'] == 'file') {
            if (request()->isAjax()) {
                $sessionConfig['path'] = App()->getRuntimePath() . config('shop.backend_index') . DS . 'session' . DS;
            } else {
                $sessionConfig['path'] = App()->getRuntimePath() . 'session' . DS;
            }

            preg_match('/adminSession=[0-9a-z]{0,32}/', request()->header()['cookie'], $matches);
            $sessionId = str_replace($sessionConfig['name'] . '=', '', str_replace('adminSession=', '', $matches[0]));
            $content = (new File(App(), $sessionConfig))->read($sessionId);
            $content = unserialize($content);

            if (empty($content)) {
                return true;
            }

            $this->admin = $content;
            return false;
        }

        return false;
    }

    /**
     * 获取插件配置
     * @return array
     */
    protected function getConfig() : array
    {
        $addonsPath = App::getRootPath() . 'addons';
        $addonsDir = scandir($addonsPath);
        $config = [];

        foreach ($addonsDir as $vo) {
            $configFile = $addonsPath . DS . $vo . DS . 'config.php';
            if (is_file($configFile)) {

                $tempArr = (array)include $configFile;
                $config = array_merge($config, $tempArr);
            }
        }

        return $config;
    }
}
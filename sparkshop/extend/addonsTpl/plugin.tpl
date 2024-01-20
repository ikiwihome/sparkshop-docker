<?php

namespace addons\<<pluginName>>;

use app\model\system\AdminNode;
use plugins\Addons;
use utils\SqlTool;

class Plugin extends Addons
{
    // 事件注册
    public static function hooks()
    {
        return [
            '<<pluginName>>' => [

            ]
        ];
    }

    /**
     * 插件安装方法
     * @return array
     */
    public static function install() : array
    {
        // 写菜单
        $adminNodeModel = new AdminNode();
        $menu = self::getMenu();

        // 菜单最多三级
        foreach ($menu as $vo) {
            $pid = $adminNodeModel->insertOne(makeMenuItem($vo['pid'], '<<pluginName>>', $vo['name'], $vo['path'], $vo['icon'], $vo['is_menu'], $vo['sort']))['data'];
            if (isset($vo['child']) && !empty($vo['child'])) {

                foreach ($vo['child'] as $vo1) {
                    $pid2 = $adminNodeModel->insertOne(makeMenuItem($pid, '<<pluginName>>', $vo1['name'], $vo1['path'], $vo1['icon'], $vo1['is_menu'], $vo1['sort']))['data'];
                    if (isset($vo1['child']) && !empty($vo1['child'])) {

                        foreach ($vo1['child'] as $vo2) {
                            $adminNodeModel->insertOne(makeMenuItem($pid2, '<<pluginName>>', $vo2['name'], $vo2['path'], $vo2['icon'], $vo2['is_menu'], $vo2['sort']))['data'];
                        }
                    }
                }
            }
        }

        return SqlTool::query(__DIR__ . DS . 'data' . DS . 'install.sql');
    }

    /**
     * 插件卸载方法
     * @return array
     */
    public static function uninstall() : array
    {
        return SqlTool::query(__DIR__ . DS . 'data' . DS . 'uninstall.sql');
    }

    /**
     * 插件升级方法
     * @return array
     */
    public static function update() : array
    {
        return SqlTool::query(__DIR__ . DS . 'data' . DS . 'update.sql');
    }

    /**
     * 获取插件名称
     * @return array
     */
    public static function getInfo() : array
    {
        return [
            'name' => '<<pluginName>>',
            'title' => '<<title>>',
            'icon' => '<<icon>>',
            'description' => '<<description>>',
            'author' => '<<author>>',
            'home_page' => '<<home_page>>',
            'version' => '<<version>>'
        ];
    }

    /**
     * 获取菜单
     * @return array
     */
    private static function getMenu() : array
    {
        return [

        ];
    }
}
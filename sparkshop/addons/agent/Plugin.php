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
namespace addons\agent;

use app\model\system\AdminNode;
use plugins\Addons;
use utils\SqlTool;

class Plugin extends Addons
{
    // 事件注册
    public static function hooks()
    {
        return [
            'agent' => [
                '\addons\agent\subscribe\AgentSubscribe'
            ]
        ];
    }

    /**
     * 插件安装方法
     * @return array
     */
    public static function install()
    {
        // 写菜单
        $adminNodeModel = new AdminNode();
        $menu = self::getMenu();

        // 菜单最多三级
        foreach ($menu as $vo) {
            $pid = $adminNodeModel->insertOne(makeMenuItem($vo['pid'], 'agent', $vo['name'], $vo['path'], $vo['icon'], $vo['is_menu'], $vo['sort']))['data'];
            if (isset($vo['child']) && !empty($vo['child'])) {

                foreach ($vo['child'] as $vo1) {
                    $pid2 = $adminNodeModel->insertOne(makeMenuItem($pid, 'agent', $vo1['name'], $vo1['path'], $vo1['icon'], $vo1['is_menu'], $vo1['sort']))['data'];
                    if (isset($vo1['child']) && !empty($vo1['child'])) {

                        foreach ($vo1['child'] as $vo2) {
                            $adminNodeModel->insertOne(makeMenuItem($pid2, 'agent', $vo2['name'], $vo2['path'], $vo2['icon'], $vo2['is_menu'], $vo2['sort']))['data'];
                        }
                    }
                }
            }
        }

        // 两个需要特殊写入财务模块的菜单 财务模块pid = 178
        $pid = $adminNodeModel->insertOne(makeMenuItem(178, 'agent', '佣金记录', '#', 'el-icon-s-finance', 2, 98))['data'];
        $adminNodeModel->insertOne(makeMenuItem($pid, 'agent', '佣金提现', 'addons/agent/admin.agent/applyLog', '', 2, 0));

        return SqlTool::query(__DIR__ . DS . 'data' . DS . 'install.sql');
    }

    /**
     * 插件卸载方法
     * @return array
     */
    public static function uninstall()
    {
        return SqlTool::query(__DIR__ . DS . 'data' . DS . 'uninstall.sql');
    }

    /**
     * 插件升级方法
     * @return array
     */
    public static function update()
    {
        return SqlTool::query(__DIR__ . DS . 'data' . DS . 'update.sql');
    }

    /**
     * 获取插件名称
     * @return array
     */
    public static function getInfo()
    {
        return [
            'name' => 'agent',
            'title' => '分销',
            'icon' => 'el-icon-s-promotion',
            'description' => '分销插件',
            'author' => 'NickBai',
            'home_page' => 'https://sparkshop.cn',
            'version' => '1.0.1'
        ];
    }

    /**
     * 获取菜单
     * @return array
     */
    private static function getMenu()
    {
        return [
           [
               "pid" => 0,
               "name" => "分销",
               "path" => "#",
               "icon" => "el-icon-s-promotion",
               "is_menu" => 2,
               "sort" => 88,
               "child" => [
                   [
                       "name" => "分销员",
                       "path" => "addons/agent/admin.agentUser/index",
                       "icon" => "el-icon-user",
                       "is_menu" => 2,
                       "sort" => 99,
                       "child" => [
                           [
                               "name" => "推广人",
                               "path" => "addons/agent/admin.agentUser/detail",
                               "icon" => "",
                               "is_menu" => 1,
                               "sort" => 0
                           ],
                           [
                               "name" => "取消资格",
                               "path" => "addons/agent/admin.agentUser/del",
                               "icon" => "",
                               "is_menu" => 1,
                               "sort" => 0
                           ],
                           [
                               "name" => "分销订单",
                               "path" => "addons/agent/admin.agentUser/order",
                               "icon" => "",
                               "is_menu" => 1,
                               "sort" => 0
                           ],
                           [
                               "name" => "修改等级",
                               "path" => "addons/agent/admin.agentUser/level",
                               "icon" => "",
                               "is_menu" => 1,
                               "sort" => 0
                           ],
                           [
                               "name" => "指定分销员",
                               "path" => "addons/agent/admin.agentUser/set",
                               "icon" => "",
                               "is_menu" => 1,
                               "sort" => 0
                           ]
                       ]
                   ],
                   [
                       "name" => "分销商品",
                       "path" => "addons/agent/admin.agentGoods/index",
                       "icon" => "el-icon-s-goods",
                       "is_menu" => 2,
                       "sort" => 70,
                       "child" => [
                           [
                               "name" => "删除",
                               "path" => "addons/agent/admin.agentGoods/del",
                               "icon" => "",
                               "is_menu" => 1,
                               "sort" => 0
                           ],
                           [
                               "name" => "添加",
                               "path" => "addons/agent/admin.agentGoods/add",
                               "icon" => "",
                               "is_menu" => 1,
                               "sort" => 0
                           ]
                       ]
                   ],
                   [
                       "name" => "分销等级",
                       "path" => "addons/agent/admin.agentLevel/index",
                       "icon" => "el-icon-s-marketing",
                       "is_menu" => 2,
                       "sort" => 80,
                       "child" => [
                           [
                               "name" => "删除",
                               "path" => "addons/agent/admin.agentLevel/del",
                               "icon" => "",
                               "is_menu" => 1,
                               "sort" => 0
                           ],
                           [
                               "name" => "编辑",
                               "path" => "addons/agent/admin.agentLevel/edit",
                               "icon" => "",
                               "is_menu" => 1,
                               "sort" => 0
                           ],
                           [
                               "name" => "新增",
                               "path" => "addons/agent/admin.agentLevel/add",
                               "icon" => "",
                               "is_menu" => 1,
                               "sort" => 0
                           ]
                       ]
                   ],
                   [
                       "name" => "分销设置",
                       "path" => "addons/agent/admin.agent/setting",
                       "icon" => "el-icon-setting",
                       "is_menu" => 2,
                       "sort" => 90
                   ]
               ]
           ]
        ];
    }
}
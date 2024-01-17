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
namespace app\admin\service;

use app\model\system\SysSetting;

class SystemService
{
    /**
     * 保存配置
     * @param array $param
     * @return array
     */
    public function saveSystem(array $param): array
    {
        try {

            $sysSettingModel = new SysSetting();
            foreach ($param as $key => $vo) {
                $sysSettingModel->where('key', $key)->update([
                    'value' => $vo
                ]);
            }
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0);
    }
}
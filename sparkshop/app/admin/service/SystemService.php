<?php

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
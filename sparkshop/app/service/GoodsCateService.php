<?php

namespace app\service;

use app\model\goods\GoodsCate;

class GoodsCateService
{
    /**
     * 获取分类列表
     * @return array
     */
    public function getCateList(): array
    {
        $goodsCateModel = new GoodsCate();
        $list = $goodsCateModel->getAllList(['status' => 1], 'id,pid,name,icon,level', 'sort desc')['data'];

        return dataReturn(0, 'success', $list);
    }
}
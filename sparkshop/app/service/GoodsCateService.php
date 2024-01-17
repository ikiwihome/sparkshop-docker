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
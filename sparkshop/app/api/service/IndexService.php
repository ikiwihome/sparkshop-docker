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

namespace app\api\service;

use app\model\goods\Goods;
use app\model\system\WebsiteSlider;

class IndexService
{
    /**
     * 获取首页数据
     * @return array
     */
    public function getIndexData()
    {
        $diyData = getConfByType('diy');
        return dataReturn(0, 'success', [
            'diyBg' => json_decode($diyData['miniapp_home_bg_diy'], true),
            'diyData' => json_decode($diyData['miniapp_home_diy'], true)
        ]);
    }

    /**
     * 搜索
     * @param array $param
     * @return array
     */
    public function search(array $param): array
    {
        $goodsModel = new Goods();

        $where[] = ['is_del', '=', 2];
        $where[] = ['is_show', '=', 1];
        $where[] = ['name', 'like', '%' . $param['keywords'] . '%'];
        $field = 'id,name,slider_image,sales,price,original_price';

        $list = $goodsModel->getPageList($param['limit'], $where, $field);
        $list['data']->each(function ($item) {
            $item->pic = json_decode($item['slider_image'], true)['0'];
            return $item;
        });

        return $list;
    }
}
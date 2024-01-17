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

namespace addons\agent\model;

use app\model\BaseModel;
use app\model\goods\Goods;

class AgentGoods extends BaseModel
{
    public function goods()
    {
        return $this->hasOne(Goods::class, 'id', 'goods_id')->visible(['id', 'name', 'slider_image', 'sales', 'stock']);
    }
}
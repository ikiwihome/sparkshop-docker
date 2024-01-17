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

namespace app\api\controller;

use app\service\UserCollectService;

class UserCollect extends Base
{
    /**
     * 我的收藏
     */
    public function myCollect(UserCollectService $userCollectService)
    {
        return json($userCollectService->getMyCollect(input('param.')));
    }

    /**
     * 收藏
     */
    public function add(UserCollectService $userCollectService)
    {
        return json($userCollectService->addCollect(input('param.')));
    }

    /**
     * 移除收藏
     */
    public function remove(UserCollectService $userCollectService)
    {
        return json($userCollectService->removeCollect(input('param.id')));
    }

    /**
     * 通过goods_id 移除收藏
     */
    public function removeByGoodsId(UserCollectService $userCollectService)
    {
        return json($userCollectService->removeCollectByGoodsId(input('param.goods_id')));
    }
}
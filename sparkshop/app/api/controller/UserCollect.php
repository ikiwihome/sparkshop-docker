<?php


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
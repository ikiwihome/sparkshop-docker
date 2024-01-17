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

use app\api\validate\UserCollectionValidate;
use app\model\user\UserCollection;
use think\exception\ValidateException;

class UserCollectService
{
    /**
     * 获取我的收藏
     * @param array $param
     * @return array
     */
    public function getMyCollect(array $param): array
    {
        $userCollectModel = new UserCollection();
        return $userCollectModel->getPageList($param['limit'], [
            'user_id' => getUserInfo()['id']
        ]);
    }

    /**
     * 添加/取消收藏
     * @param array $param
     * @return array
     */
    public function addCollect(array $param): array
    {
        try {

            validate(UserCollectionValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-1, $e->getError());
        }

        $userInfo = getUserInfo();
        $userCollectModel = new UserCollection();
        $has = $userCollectModel->findOne([
            'goods_id' => $param['goods_id'],
            'user_id' => $userInfo['id']
        ])['data'];
        if (!empty($has)) {
            return dataReturn(0, '收藏成功');
        }

        $param['user_id'] = $userInfo['id'];
        $param['create_time'] = now();
        $res = $userCollectModel->insertOne($param);
        if ($res['code'] != 0) {
            return $res;
        }

        return dataReturn(0, '收藏成功');
    }

    /**
     * 移除收藏
     * @param int $id
     * @return array
     */
    public function removeCollect(int $id): array
    {
        $userCollectModel = new UserCollection();
        return $userCollectModel->delByWhere([
            'user_id' => getUserInfo()['id'],
            'id' => $id
        ]);
    }

    /**
     * 移除收藏
     * @param int $goodsId
     * @return array
     */
    public function removeCollectByGoodsId(int $goodsId): array
    {
        $userCollectModel = new UserCollection();
        return $userCollectModel->delByWhere([
            'user_id' => getUserInfo()['id'],
            'goods_id' => $goodsId
        ]);
    }
}
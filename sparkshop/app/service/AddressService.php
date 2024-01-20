<?php

namespace app\service;

use app\api\validate\AddressValidate;
use app\model\user\UserAddress;
use think\exception\ValidateException;

class AddressService
{
    /**
     * 获取默认地址
     * @param int $userId
     * @return array
     */
    public function getDefaultAddress(int $userId): array
    {
        $userAddressModel = new UserAddress();
        $addressInfo = $userAddressModel->findOne([
            'user_id' => $userId,
            'is_del' => 1,
            'is_default' => 1
        ])['data'];

        return dataReturn(0, 'success', $addressInfo);
    }

    /**
     * 获取用户的地址
     * @param int $userId
     * @return array
     */
    public function getUserAddressList(int $userId): array
    {
        $userAddressModel = new UserAddress();
        $addressList = $userAddressModel->getAllList([
            'user_id' => $userId,
            'is_del' => 1
        ], '*', 'is_default asc, id desc')['data'];

        return dataReturn(0, 'success', $addressList);
    }

    /**
     * 添加用户地址
     * @param array $param
     * @param int $userId
     * @return array
     */
    public function addUserAddress(array $param, int $userId): array
    {
        try {
            validate(AddressValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-2, $e->getError());
        }

        $param['create_time'] = now();
        $param['user_id'] = $userId;

        $userAddressModel = new UserAddress();
        if ($param['is_default'] == 1) {
            $userAddressModel->updateByWhere([
                'is_default' => 2
            ], ['user_id' => $param['user_id']]);
        }

        return $userAddressModel->insertOne($param);
    }

    /**
     * 编辑用户地址
     * @param array $param
     * @param int $userId
     * @return array
     */
    public function editUserAddress(array $param, int $userId): array
    {
        try {
            validate(AddressValidate::class)->check($param);
        } catch (ValidateException $e) {
            return dataReturn(-2, $e->getError());
        }

        $param['update_time'] = now();
        $param['user_id'] = $userId;

        $userAddressModel = new UserAddress();
        if ($param['is_default'] == 1) {
            $userAddressModel->updateByWhere([
                'is_default' => 2
            ], ['user_id' => $param['user_id']]);
        }

        return $userAddressModel->updateByWhere($param, [
            'id' => $param['id'],
            'user_id' => $userId
        ]);
    }

    /**
     * 删除用户地址
     * @param int $id
     * @param int $userId
     * @return array
     */
    public function delUserAddress(int $id, int $userId): array
    {
        $userAddressModel = new UserAddress();
        return $userAddressModel->delByWhere([
            'id' => $id,
            'user_id' => $userId
        ]);
    }

    /**
     * 设置默认地址
     * @param int $id
     * @param int $userId
     * @return array
     */
    public function setDefault(int $id, int $userId): array
    {
        $userAddressModel = new UserAddress();
        $userAddressModel->updateByWhere(['is_default' => 2], [
            'user_id' => $userId
        ]);

        $userAddressModel->updateByWhere(['is_default' => 1], [
            'user_id' => $userId,
            'id' => $id
        ]);

        return dataReturn(0);
    }
}
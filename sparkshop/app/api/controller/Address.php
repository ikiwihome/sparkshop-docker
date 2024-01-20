<?php

namespace app\api\controller;

use app\admin\service\CityService;
use app\service\AddressService;

class Address extends Base
{
    /**
     * 添加地址
     */
    public function add(AddressService $addressService)
    {
        if (request()->isPost()) {
            return json($addressService->addUserAddress(input('post.'), $this->user['id']));
        }
    }

    /**
     * 编辑地址
     */
    public function edit(AddressService $addressService)
    {
        return json($addressService->editUserAddress(input('post.'), $this->user['id']));
    }

    /**
     * 删除地址
     */
    public function del(AddressService $addressService)
    {
        return json($addressService->delUserAddress(input('param.id'), $this->user['id']));
    }

    /**
     * 设置默认地址
     */
    public function setDefault(AddressService $addressService)
    {
        return json($addressService->setDefault(input('param.id'), $this->user['id']));
    }

    /**
     * 获取默认的地址
     */
    public function getDefaultAddress(AddressService $addressService)
    {
        return json($addressService->getDefaultAddress($this->user['id']));
    }

    /**
     * 获取用户地址
     */
    public function getUserAddress(AddressService $addressService)
    {
        return json($addressService->getUserAddressList($this->user['id']));
    }

    /**
     * 省市区数据
     */
    public function area(CityService $cityService)
    {
        return json($cityService->getAreaTree());
    }
}
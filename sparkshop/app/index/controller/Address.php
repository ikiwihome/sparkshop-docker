<?php
// +----------------------------------------------------------------------
// | SparkShop 坚持做优秀的商城系统
// +----------------------------------------------------------------------
// | Copyright (c) 2022~2099 http://sparkshop.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai
// +----------------------------------------------------------------------
namespace app\index\controller;

use app\model\user\UserAddress;
use app\service\AddressService;
use think\facade\View;

class Address extends Base
{
    public function initialize()
    {
        parent::initialize();
        pcLoginCheck();
    }

    /**
     * 添加地址
     */
    public function add(AddressService $addressService)
    {
        if (request()->isPost()) {

            return json($addressService->addUserAddress(input('post.'), session('home_user_id')));
        }

        $ip = request()->ip();
        $location = getLocationByIp($ip, 2);

        if ($location['province'] == "0") {
            $province = "北京市";
            $city = "北京市";
            $county = "东城区";
        } else {
            $province = $location['province'];
            $city = $location['city'];
            $county = "";
        }

        View::assign([
            'province' => $province,
            'city' => $city,
            'county' => $county
        ]);

        return View::fetch();
    }

    /**
     * 编辑地址
     */
    public function edit(AddressService $addressService, UserAddress $userAddressModel)
    {
        if (request()->isPost()) {

            return json($addressService->editUserAddress(input('post.'), session('home_user_id')));
        }

        View::assign([
            'info' => $userAddressModel->getInfoById(input('param.id'))['data']
        ]);

        return View::fetch();
    }

    /**
     * 删除地址
     */
    public function del(AddressService $addressService)
    {
        if (request()->isAjax()) {

            return json($addressService->delUserAddress(input('param.id'), session('home_user_id')));
        }
    }

    /**
     * 设置默认地址
     */
    public function setDefault(AddressService $addressService)
    {
        if (request()->isAjax()) {

            return json($addressService->setDefault(input('param.id'), session('home_user_id')));
        }
    }

    /**
     * 获取用户地址
     */
    public function getUserAddress(AddressService $addressService)
    {
        if (request()->isAjax()) {

            return json($addressService->getUserAddressList(session('home_user_id')));
        }
    }
}
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

use app\service\UserService;

class User extends Base
{
    /**
     * 获取用户中心首页数据
     */
    public function index(UserService $userService)
    {
        return json($userService->getMyBaseInfo($this->user['id']));
    }

    /**
     * 获取用户基础数据
     */
    public function info(UserService $userService)
    {
        return json($userService->getUserInfo($this->user['id']));
    }

    /**
     * 修改用户信息
     */
    public function update(UserService $userService)
    {
        return json($userService->updateInfo(input('post.')));
    }

    /**
     * 修改绑定手机号
     */
    public function changePhone(UserService $userService)
    {
        $param = input('post.');
        $param['user_id'] = $this->user['id'];

        return json($userService->changePhone($param));
    }

    /**
     * 修改密码
     */
    public function changePassword(UserService $userService)
    {
        $param = input('post.');
        $param['user_id'] = $this->user['id'];

        return json($userService->changePassword($param));
    }
}
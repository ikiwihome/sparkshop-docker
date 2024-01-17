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

use app\api\service\LoginService;
use app\BaseController;

class Login extends BaseController
{
    public function initialize()
    {
        crossDomain();
    }

    /**
     * 根据用户名登录
     */
    public function loginBySms(LoginService $loginService)
    {
        return json($loginService->doLoginBySms(input('post.')));
    }

    /**
     * 根据账号登录
     */
    public function loginByAccount(LoginService $loginService)
    {
        return json($loginService->doLoginByAccount(input('post.')));
    }

    /**
     * 注册
     */
    public function reg(LoginService $loginService)
    {
        return json($loginService->userReg(input('post.')));
    }

    /**
     * 根据微信登录
     */
    public function loginByWechat(LoginService $loginService)
    {
        return json($loginService->loginByWechat(input('post.')));
    }
}
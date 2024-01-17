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

use app\BaseController;
use app\index\service\LoginService;
use think\facade\View;

class Login extends BaseController
{
    /**
     * 登录
     */
    public function index(LoginService $loginService)
    {
        if (request()->isPost()) {

            return json($loginService->doLogin(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 注册
     */
    public function reg(LoginService $loginService)
    {
        if (request()->isPost()) {

            return json($loginService->doReg(input('post.')));
        }

        return View::fetch();
    }

    /**
     * 退出登录
     */
    public function loginOut()
    {
        session('home_user_id', null);
        session('home_user_name', null);
        session('home_user_avatar', null);

        return redirect('/');
    }

    /**
     * 验证码
     */
    public function captcha()
    {
        View::assign([
            'phone' => input('param.phone'),
            'type' => input('param.type')
        ]);

        return View::fetch();
    }

    /**
     * 忘记密码
     */
    public function forget(LoginService $loginService)
    {
        if (request()->isPost()) {

            return json($loginService->forgetPassword(input('post.')));
        }

        return View::fetch();
    }
}
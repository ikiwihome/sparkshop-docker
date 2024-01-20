<?php


namespace app\index\controller;

use app\BaseController;
use app\service\CartService;
use think\facade\App;
use think\facade\View;

class Base extends BaseController
{
    public function initialize()
    {
        // 检测站点是否关闭
        $isClose = getConfByType('base')['is_open'];
        if ($isClose != 1) {
            exit(file_get_contents(App::getAppPath() . 'view' . DS . 'default' . DS . 'close.html'));
        }

        $cartService = new CartService();
        View::assign([
            'userInfo' => getFrontUserInfo(),
            'conf' => getConfByType('base'),
            'cartNum' => empty(session('home_user_id')) ? 0.00 : $cartService->getUserCartNum(session('home_user_id'))['data'],
            'cartAmount' => empty(session('home_user_id')) ? 0.00 : number_format($cartService->getUserCartAmount(session('home_user_id'))['data'], 2)
        ]);
    }
}
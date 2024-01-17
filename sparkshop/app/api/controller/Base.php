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

use app\BaseController;

class Base extends BaseController
{
    protected $user;

    public function initialize()
    {
        crossDomain();

        $this->user = getJWT(getHeaderToken());
        if (empty($this->user)) {
            exit(json_encode(['code' => 403, 'data' => [], 'msg' => '请登录']));
        }
    }
}
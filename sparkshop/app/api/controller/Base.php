<?php

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
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

namespace addons\agent\model;

use app\model\BaseModel;
use app\model\user\User;

class AgentOrder extends BaseModel
{
    public function agentUser()
    {
        return $this->hasOne(User::class, 'id', 'agent_user_id')->visible(['nickname', 'phone']);
    }
}
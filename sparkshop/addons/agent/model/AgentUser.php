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

class AgentUser extends BaseModel
{
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->visible(['nickname', 'phone', 'avatar']);
    }

    public function level()
    {
        return $this->hasOne(AgentLevel::class, 'id', 'level_id')->visible(['name']);
    }

    public function parent()
    {
        return $this->hasOne(User::class, 'id', 'parent_spread_id')->visible(['nickname', 'id']);
    }
}
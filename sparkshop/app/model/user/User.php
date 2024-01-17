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

namespace app\model\user;

use app\model\BaseModel;

class User extends BaseModel
{
    public function level()
    {
        return $this->hasOne(UserLevel::class, 'id', 'level_id');
    }

    public function group()
    {
        return $this->hasOne(UserGroup::class, 'id', 'group_id');
    }
}


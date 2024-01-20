<?php


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


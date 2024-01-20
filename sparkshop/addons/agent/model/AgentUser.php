<?php


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
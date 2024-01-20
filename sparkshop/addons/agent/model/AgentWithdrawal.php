<?php


namespace addons\agent\model;

use app\model\BaseModel;
use app\model\user\User;

class AgentWithdrawal extends BaseModel
{
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->visible(['nickname', 'phone']);
    }
}
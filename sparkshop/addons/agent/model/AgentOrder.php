<?php


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
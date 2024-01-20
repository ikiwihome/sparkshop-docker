<?php


namespace app\model\system;

use app\model\BaseModel;

class AdminUser extends BaseModel
{
    public function role()
    {
        return $this->hasOne(AdminRole::class, 'id', 'role_id');
    }
}
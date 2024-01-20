<?php


namespace app\model\system;

use app\model\BaseModel;

class ShippingTemplatesRegion extends BaseModel
{
    public function province()
    {
        return $this->hasOne(SetCity::class, 'id', 'province_id');
    }

    public function city()
    {
        return $this->hasOne(SetCity::class, 'id', 'city_id');
    }
}

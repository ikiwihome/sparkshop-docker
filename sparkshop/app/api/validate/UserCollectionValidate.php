<?php


namespace app\api\validate;

use think\Validate;

class UserCollectionValidate extends Validate
{
    protected $rule = [
        'goods_id|商品id' => 'require|number',
        'goods_name|商品名称' => 'require',
        'goods_pic|商品图片' => 'require',
        'price|商品价格' => 'require'
    ];
}
<?php


namespace app\admin\validate;

use think\Validate;

class WebsiteSliderValidate extends Validate
{
    protected $rule = [
        'name|轮播描述' => 'require',
        'target_url|链接地址' => 'require',
        'pic_url|图片地址' => 'require',
        'type|链接类型' => 'require',
        'sort|排序值' => 'require',
    ];
}
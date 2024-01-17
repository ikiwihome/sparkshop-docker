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
namespace strategy\store;

use strategy\store\impl\AliyunImpl;
use strategy\store\impl\QCloudImpl;
use strategy\store\impl\QiniuImpl;

class StoreProvider
{
    protected $strategy = null;

    public function __construct($type, $config)
    {
        if ($type == 'aliyun') {
            $this->strategy = new AliyunImpl($config);
        } else if ($type == 'qiniu') {
            $this->strategy = new QiniuImpl($config);
        } else if ($type == 'qcloud') {
            $this->strategy = new QCloudImpl($config);
        }
    }

    public function getStrategy()
    {
        return $this->strategy;
    }
}
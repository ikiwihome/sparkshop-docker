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
namespace strategy\notify;

use strategy\express\impl\AliyunImpl;
use strategy\notify\impl\BalanceNotifyImpl;
use strategy\notify\impl\OrderNotifyImpl;

class NotifyProvider
{
    protected $strategy;

    public function __construct($orderNo)
    {
        $business = substr($orderNo, 0, 1);
        if ($business == 'P') {
            $this->strategy = new OrderNotifyImpl();
        } else if ($business == 'B') {
            $this->strategy = new BalanceNotifyImpl();
        }
    }

    public function getStrategy()
    {
        return $this->strategy;
    }
}
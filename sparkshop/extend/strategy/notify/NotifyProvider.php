<?php

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
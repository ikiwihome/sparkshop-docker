<?php

namespace strategy\pay;

use strategy\pay\impl\AlipayImpl;
use strategy\pay\impl\BalanceImpl;
use strategy\pay\impl\WechatPayImpl;

class PayProvider
{
    protected $strategy;

    public function __construct($type)
    {
        if ($type == 'alipay') {
            $this->strategy = new AlipayImpl();
        } else if ($type == 'wechat_pay') {
            $this->strategy = new WechatPayImpl();
        } else if ($type == 'balance') {
            $this->strategy = new BalanceImpl();
        }
    }

    public function getStrategy()
    {
        return $this->strategy;
    }

    public function payByPlatform($platform, $payWay, $payParam)
    {
        // pc端支付
        if (empty($platform)) {
            return $this->getStrategy()->pay($payParam);
        }

        // 微信小程序支付
        if ($platform == 'miniapp' && $payWay == 'wechat_pay') {
            return $this->getStrategy()->miniappPay($payParam);
        }

        // 小程序端支付
        if ($platform == 'h5') {
            return $this->getStrategy()->web($payParam);
        }

        // APP支付
        if ($platform == 'app') {
            return $this->getStrategy()->app($payParam);
        }

        // 余额支付
        return $this->getStrategy()->pay($payParam);
    }
}
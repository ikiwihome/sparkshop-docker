<?php

namespace strategy\pay\impl;

use strategy\pay\PayInterface;
use Yansongda\Pay\Pay;

class AlipayImpl implements PayInterface
{
    private $config = [];
    protected $notifyUrl = '';

    public function __construct()
    {
        $formatConfig = getConfByType('alipay');
        $this->setConfig([
                'app_id' => $formatConfig['alipay_app_id'],
                'notify_url' => $this->notifyUrl,
                'ali_public_key' => $formatConfig['alipay_public_key'],
                'private_key' => $formatConfig['alipay_private_key'],
                'log' => [
                    'file' => './logs/alipay.log',
                    'level' => 'info',
                    'type' => 'single',
                    'max_file' => 30,
                ],
                'http' => [
                    'timeout' => 5.0,
                    'connect_timeout' => 5.0
                ],
                //'mode' => 'dev'
            ])->setNotifyUrl(getConfByType('base')['website_url'] . '/index/notify/alipay');
    }

    public function pay($param)
    {
        return Pay::alipay($this->config)->scan($param);
    }

    public function web($param)
    {
        $this->config['return_url'] = $param['return_url'];
        return Pay::alipay($this->config)->wap($param)->getContent();
    }

    public function app($param)
    {
        return Pay::alipay($this->config)->app([
            'out_trade_no' => $param['out_trade_no'],
            'total_amount' => $param['total_amount'],
            'subject'      => mb_substr($param['subject'], 0, 30),
        ])->getContent();
    }

    public function refund($param)
    {
        $order = [
            'out_trade_no' => $param['order_no'],
            'refund_amount' => $param['refund_price']
        ];

        $result = Pay::alipay($this->config)->refund($order);
        return dataReturn(0, '退款成功', $result);
    }

    public function close($param)
    {
        // TODO: Implement close() method.
    }

    public function getObject()
    {
        return Pay::alipay($this->config);
    }

    public function setNotifyUrl(string $url)
    {
        $this->config['notify_url'] = $url;
        return $this;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }
}
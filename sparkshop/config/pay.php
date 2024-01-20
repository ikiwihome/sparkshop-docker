<?php


return [

    // 支付方式
    'pay_way' => [
        [
            'type' => 'wechat_pay',
            'icon' => 'iconfont icon-iconfontweixin'
        ],
        [
            'type' => 'alipay',
            'icon' => 'iconfont icon-alipay'
        ],
    ],

    'pay_type' => [
        'wechat_pay' => 1,
        'alipay' => 2,
        'balance' => 3
    ],

    'pay_id_type' => [
        1 => 'wechat_pay',
        2 => 'alipay',
        3 => 'balance'
    ],

    // 需要加入订单检测的支付方式
    'overdue_pay_type' => [
        'wechat_pay' => 1,
        'alipay' => 1
    ]
];
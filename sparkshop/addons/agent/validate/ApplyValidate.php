<?php


namespace addons\agent\validate;

use think\Validate;

class ApplyValidate extends Validate
{
    protected $rule = [
        'type|提现方式' => 'require',
        'card_no|卡号' => 'require',
        'name|持卡人名' => 'require',
        'bank|所属银行' => 'require',
        'apply_amount|提现金额' => 'require|float',
        'account|收款账号' => 'require',
        'receive_qrcode|收款码' => 'require'
    ];

    protected $scene = [
        'bank' => ['type', 'card_no', 'name', 'bank', 'apply_amount'],
        'other' => ['type', 'account', 'receive_qrcode', 'apply_amount']
    ];
}
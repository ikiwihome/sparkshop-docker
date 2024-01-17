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
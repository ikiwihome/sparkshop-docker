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
namespace app\model\system;

use app\model\BaseModel;

class SysSetting extends BaseModel
{
    /**
     * 获取打开的支付方式
     * @return array
     */
    public function getOpenWay()
    {
        try {

            $res = $this->field('value,type')->whereIn('key', ['wechat_pay_open', 'alipay_open', 'balance_open'])->select();
            $payMap = [];
            if (!empty($res)) {
                foreach ($res as $vo) {
                    $payMap[$vo['type']] = $vo['value'];
                }
            }
        } catch (\Exception $e) {
            return dataReturn(-1, $e->getMessage());
        }

        return dataReturn(0, 'success', $payMap);
    }
}
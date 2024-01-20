<?php

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
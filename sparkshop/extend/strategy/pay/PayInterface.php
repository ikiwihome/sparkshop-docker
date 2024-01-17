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
namespace strategy\pay;

interface PayInterface
{
    /**
     * 支付pc
     * @param $param
     * @return mixed
     */
    public function pay($param);

    /**
     * web (h5支付)
     * @param $param
     * @return mixed
     */
    public function web($param);

    /**
     * app支付
     * @param $param
     * @return mixed
     */
    public function app($param);

    /**
     * 退款
     * @param $param
     * @return mixed
     */
    public function refund($param);

    /**
     * 关闭订单
     * @param $param
     * @return mixed
     */
    public function close($param);

    /**
     * 获取支付对象
     * @return mixed
     */
    public function getObject();

    /**
     * 设置回调地址
     * @return mixed
     */
    public function setNotifyUrl(string $url);
}
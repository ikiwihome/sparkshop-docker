<?php

namespace strategy\notify;

interface NotifyInterface
{
    /**
     * 处理订单
     */
    public function dealOrder($data, $way);
}
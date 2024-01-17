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
namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;

class AutoSettlement extends Command
{
    protected function configure()
    {
        $this->setName('autoSettlement')
            ->setDescription('SparkShop商城佣金自动结算');
    }

    protected function execute(Input $input, Output $output)
    {
        if (hasInstalled('agent')) {
            event('AutoSettlement');
        }
    }
}
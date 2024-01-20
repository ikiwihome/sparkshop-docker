<?php

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
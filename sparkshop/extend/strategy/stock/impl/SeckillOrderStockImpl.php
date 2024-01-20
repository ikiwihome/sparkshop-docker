<?php


namespace strategy\stock\impl;

use strategy\stock\StockInterface;

class SeckillOrderStockImpl implements StockInterface
{
    public function dealStockAndSales($param)
    {
        return event('SeckillStock', $param)[0];
    }
}
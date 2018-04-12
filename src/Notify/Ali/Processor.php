<?php
namespace EasyPay\Notify\Ali;

use EasyPay\TradeData\Ali\TradeData;
use EasyPay\Interfaces\NotifyProcessorInterface;

class Processor implements NotifyProcessorInterface
{
    public function getNotify()
    {
        if (empty($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'GET') {
            throw new \Exception('无法处理的请求');
        }

        $data = new TradeData($_GET);
        $data->verifyRequestSign();

        return $data;
    }
}
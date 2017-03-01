<?php
namespace EasyPay\Notify\Ali;

use EasyPay\DataManager\Ali\Data;
use EasyPay\Interfaces\NotifyProcessorInterface;

class Processor implements NotifyProcessorInterface
{
    public function getNotify()
    {
        if (empty($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'GET') {
            throw new \Exception('无法处理的请求');
        }

        $data = new Data($_GET);
        $data->verifyRequestSign();

        return $data;
    }
}
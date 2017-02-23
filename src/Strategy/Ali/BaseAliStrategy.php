<?php
namespace EasyPay\Strategy\Ali;

use EasyPay\Config;
use FastHttp\Client;
use EasyPay\DataManager\Ali\Data;
use EasyPay\Interfaces\StrategyInterface;

abstract class BaseAliStrategy implements StrategyInterface
{
    const WAP_PAY = 'alipay.trade.wap.pay';

    const QUERY_ORDER = 'alipay.trade.query';

    const CLOSE_ORDER = 'alipay.trade.close';

    const REFUND = 'alipay.trade.refund';

    const REFUND_QUERY = 'alipay.trade.fastpay.refund.query';

    const DOWN_LOAD_BILL = 'alipay.data.dataservice.bill.downloadurl.query';

    protected $payData;

    public function __construct($options)
    {
        $options = array_merge(Config::ali(), $options);
        $this->payData = new Data($options);
    }

    public function execute()
    {
        $data = $this->buildData();

        return $this->handleData($data);
    }

    protected function handleData($data)
    {
        return $data;
    }

    protected function sendHttpRequest($method, $url, $body)
    {
        // 初始化Http客户端
        $client = new Client($method, $url);

        return $client->send((string)$body);
    }

    abstract protected function buildData();
}
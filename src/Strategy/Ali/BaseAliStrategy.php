<?php
namespace EasyPay\Strategy\Ali;

use EasyPay\Config;
use EasyPay\Utils\HttpClient;
use EasyPay\DataManager\Ali\Data;
use EasyPay\Interfaces\StrategyInterface;

abstract class BaseAliStrategy implements StrategyInterface
{
    const WAP_PAY = 'alipay.trade.wap.pay';

    const QR_PAY = 'alipay.trade.precreate';

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

    protected function sendHttpRequest($method, $url, $body = null)
    {
        // 初始化Http客户端
        $client = new HttpClient($method, $url);

        return $client->send((string)$body);
    }

    protected function getServerUrl()
    {
        // 支持沙箱测试
        return Config::ali('is_sand_box')
            ? "https://openapi.alipaydev.com/gateway.do"
            : "https://openapi.alipay.com/gateway.do";
    }

    abstract protected function buildData();
}
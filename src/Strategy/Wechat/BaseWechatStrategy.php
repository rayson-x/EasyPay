<?php
namespace EasyPay\Strategy\Wechat;

use EasyPay\Config;
use EasyPay\Interfaces\StrategyInterface;
use FastHttp\Client;

/**
 * Todo 将Http Client抽象为接口,实现以流,异步的方式进行请求
 *
 * Class BaseWechatStrategy
 * @package EasyPay\Strategy\Wechat
 */
abstract class BaseWechatStrategy implements StrategyInterface
{
    // 发起订单URL
    const INIT_ORDER_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    // 查询订单URL
    const QUERY_ORDER_URL = 'https://api.mch.weixin.qq.com/pay/orderquery';
    // 关闭订单URL
    const CLOSE_ORDER_URL = 'https://api.mch.weixin.qq.com/pay/closeorder';
    // 退款URL
    const REFUND_URL = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    // 查询退款URL
    const REFUND_QUERY_URL = 'https://api.mch.weixin.qq.com/pay/refundquery';
    // 下载对账单地址
    const DOWN_LOAD_BILL_URL = 'https://api.mch.weixin.qq.com/pay/downloadbill';
    // 微信转账地址
    const TRANSFERS_URL = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";

    /**
     * @param array $option
     */
    public function __construct(array $option)
    {
        $this->payData = new Data($option);
    }

    /**
     * 发起一次Http请求
     *
     * @param $method
     * @param $url
     * @param $body
     * @return Data
     */
    protected function sendHttpRequest($method, $url, $body)
    {
        // 初始化Http客户端
        $client = new Client($method, $url);

        if (Config::wechat('ssl_key_path') && Config::wechat('ssl_cert_path')) {
            // 添加SSL证书
            $client->setCurlOption([
                'CURLOPT_SSLKEY'        =>  Config::wechat('ssl_key_path'),
                'CURLOPT_SSLCERT'       =>  Config::wechat('ssl_cert_path'),
                'CURLOPT_SSLKEYTYPE'    =>  'PEM',
                'CURLOPT_SSLCERTTYPE'   =>  'PEM',
            ]);
        }

        $response = $client->send((string)$body);
        // 解析响应Xml内容
        $result = Data::createDataFromXML((string)$response->getBody());
        // 检查是否正确
        $result->checkResult();

        return $result;
    }
}
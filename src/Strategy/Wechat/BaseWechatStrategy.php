<?php
namespace EasyPay\Strategy\Wechat;

use EasyPay\Config;
use FastHttp\Client;
use EasyPay\Strategy\BaseStrategy;
use EasyPay\DataManager\Wechat\Data;

/**
 * Class BaseWechatStrategy
 * @package EasyPay\Strategy\Wechat
 */
abstract class BaseWechatStrategy extends BaseStrategy
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

    public function execute()
    {
        // 发起Http请求
        $response = $this->sendHttpRequest(
            $this->getRequestMethod(),
            $this->getRequestTarget(),
            $this->buildData()
        );

        // 解析响应Xml内容
        $result = Data::createDataFromXML($response->getBody()->getContents());
        // 检查是否正确
        $result->checkResult();

        return $this->handleResult($result);
    }

    /**
     * 发起一次Http请求
     *
     * @param $method
     * @param $url
     * @param $body
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function sendHttpRequest($method, $url, $body)
    {
        // 初始化Http客户端
        $client = new Client($method, $url);

        // 如果设置了SSL证书与秘钥,自动启用SSL
        if (Config::wechat('ssl_key_path') && Config::wechat('ssl_cert_path')) {
            // 添加SSL证书
            $client->setCurlOption([
                'CURLOPT_SSLKEY'        =>  Config::wechat('ssl_key_path'),
                'CURLOPT_SSLCERT'       =>  Config::wechat('ssl_cert_path'),
                'CURLOPT_SSLKEYTYPE'    =>  'PEM',
                'CURLOPT_SSLCERTTYPE'   =>  'PEM',
            ]);
        }

        return $client->send((string)$body);
    }
}
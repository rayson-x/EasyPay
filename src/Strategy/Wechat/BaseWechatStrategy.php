<?php
namespace EasyPay\Strategy\Wechat;

use EasyPay\Config;
use EasyPay\Utils\HttpClient;
use EasyPay\Exception\PayException;
use EasyPay\DataManager\Wechat\Data;
use EasyPay\Exception\PayFailException;
use EasyPay\Interfaces\StrategyInterface;

/**
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
    // 微信企业转账查询
    const TRANSFERS_QUERY_URL = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo";

    /**
     * @var Data
     */
    protected $payData;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $option = array_merge(Config::wechat(), $options);
        $this->payData = new Data($option);
    }

    /**
     * 请求接口,并验证返回数据
     *
     * @return Data
     */
    public function execute()
    {
        // 发起Http请求
        $response = $this->sendHttpRequest(
            $this->getRequestMethod(),
            $this->getRequestTarget(),
            $this->buildData()
        );

        return $this->handleData((string) $response->getBody());
    }

    /**
     * 处理返回数据
     *
     * @param $result
     * @return Data
     */
    protected function handleData($result)
    {
        // 解析响应Xml内容
        $data = Data::createDataFromXML($result);

        // 通信是否成功
        if (!$data->isSuccess($data['return_code'])) {
            throw new PayException($data, $data['return_msg']);
        }

        // 交易是否发起
        if (!$data->isSuccess($data['result_code'])) {
            //抛出错误码与错误信息
            throw new PayFailException(
                $data, $data['err_code_des'], $data['err_code']
            );
        }

        // 校验签名
        $data->verifySign();

        return $data;
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
        $client = new HttpClient($method, $url);

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

    /**
     * 生成数据
     *
     * @return mixed
     */
    protected function buildData()
    {
        // 检查必要参数是否存在
        $this->payData->checkParamsExits($this->getRequireParams());
        // 填入所有可用参数,并将不可用参数清除
        $this->payData->selectedParams($this->getFillParams());

        return $this->payData;
    }

    /**
     * 获取必填参数
     *
     * @return array
     */
    abstract protected function getRequireParams();

    /**
     * 获取所有参数
     *
     * @return array
     */
    abstract protected function getFillParams();

    /**
     * 获取请求的Http动词
     *
     * @return mixed
     */
    abstract protected function getRequestMethod();

    /**
     * 获取请求的目标
     *
     * @return string
     */
    abstract protected function getRequestTarget();
}
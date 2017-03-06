<?php
namespace EasyPay\Strategy\Ali;

use EasyPay\Config;
use EasyPay\Utils\HttpClient;
use EasyPay\DataManager\Ali\Data;
use EasyPay\Interfaces\StrategyInterface;

/**
 * 支付宝基础策略
 *
 * Class BaseAliStrategy
 * @package EasyPay\Strategy\Ali
 */
abstract class BaseAliStrategy implements StrategyInterface
{
    // 移动端网页支付
    const WAP_PAY = 'alipay.trade.wap.pay';

    // 扫码支付
    const QR_PAY = 'alipay.trade.precreate';

    // 查询订单
    const QUERY_ORDER = 'alipay.trade.query';

    // 关闭订单
    const CLOSE_ORDER = 'alipay.trade.close';

    // 退款
    const REFUND = 'alipay.trade.refund';

    // 查询退款记录
    const REFUND_QUERY = 'alipay.trade.fastpay.refund.query';

    // 下载订单
    const DOWN_LOAD_BILL = 'alipay.data.dataservice.bill.downloadurl.query';

    // 企业转账
    const TRANSFERS = 'alipay.fund.trans.toaccount.transfer';
    
    /**
     * @var Data
     */
    protected $payData;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $options = array_merge(Config::ali(), $options);
        $this->payData = new Data($options);
    }

    /**
     * @return array|object
     */
    public function execute()
    {
        $data = $this->buildData();

        return $this->handleData($data);
    }

    /**
     * 构造请求数据
     *
     * @return array
     */
    protected function buildData()
    {
        // 检查必填参数是否存在
        $this->payData->checkParamsExits($this->getRequireParams());
        // 设置请求的方法
        $this->payData['method'] = $this->getMethod();
        // 生成请求参数
        $this->payData['biz_content'] = $this->buildBinContent();
        // 填入所有可用参数,并将不可用参数清除
        $this->payData->selectedParams($this->getFillParams());
        // 生成签名
        $this->payData['sign'] = $this->payData->makeSign();

        return $this->payData->toArray();
    }

    /**
     * 处理构造完成的请求数据
     *
     * @param $data
     * @return array|object
     */
    protected function handleData($data)
    {
        // 构造请求API
        $url = $this->getServerUrl() . "?" . http_build_query($data);
        // 请求支付宝服务器
        $response = $this->sendHttpRequest('POST', $url);
        // 解析响应内容
        $data = Data::createDataFromJson((string)$response->getBody());
        // 验证签名
        $data->verifyResponseSign();

        return $data;
    }

    /**
     * 发送http请求
     *
     * @param $method
     * @param $url
     * @param null $body
     * @return \Ant\Http\Response
     */
    protected function sendHttpRequest($method, $url, $body = null)
    {
        // 初始化Http客户端
        $client = new HttpClient($method, $url);

        return $client->send((string)$body);
    }

    /**
     * 获取支付宝api地址
     *
     * @return string
     */
    protected function getServerUrl()
    {
        // 支持沙箱测试
        return Config::ali('is_sand_box')
            ? "https://openapi.alipaydev.com/gateway.do"
            : "https://openapi.alipay.com/gateway.do";
    }

    /**
     * 获取请求的方法
     *
     * @return string
     */
    abstract protected function getMethod();

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
     * 生成请求数据
     *
     * @return array
     */
    abstract protected function buildBinContent();
}
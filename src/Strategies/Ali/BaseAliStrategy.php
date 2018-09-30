<?php
namespace EasyPay\Strategies\Ali;

use EasyPay\Config;
use EasyPay\Utils\HttpClient;
use EasyPay\TradeData\Ali\TradeData;
use EasyPay\Interfaces\StrategyInterface;

/**
 * Class BaseAliStrategy
 * @package EasyPay\Strategies\Ali
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
    const TRANSFER = 'alipay.fund.trans.toaccount.transfer';

    // 转账查询
    const TRANSFER_QUERY = 'alipay.fund.trans.order.query ';
    
    /**
     * @var TradeData
     */
    protected $payData;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $options = array_merge(Config::ali(), $options);

        $data = array_intersect_key($options, [
            'app_id' => true, 
            'format' => true, 
            'charset' => true,
            'version' => true,
            'sign_type' => true,
        ]);

        $this->payData = new TradeData($data, $options);
    }

    /**
     * @return array|object
     */
    public function execute($payData = [])
    {
        $this->payData->setAttributes($payData);

        $data = $this->buildData();

        return $this->handleData($data->toArray());
    }

    /**
     * 构造请求数据
     *
     * @return TradeData
     */
    protected function buildData()
    {
        // 检查必填参数是否存在
        $this->payData->checkParamsEmpty($this->getRequireParams());
        // 设置请求的方法
        $this->payData['method'] = $this->getMethod();
        // 生成请求参数
        $this->payData['biz_content'] = $this->buildBinContent();
        // 选中接口全部可用参数
        $this->payData->selected($this->getFillParams());
        // 生成签名
        $this->payData->setSign();

        return $this->payData;
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
        $data = TradeData::createFromJson((string) $response->getBody());
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

        return $client->send((string) $body);
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
     * @return mixed
     */
    public function __get($name)
    {
        return $this->payData->offsetGet($name);
    }

    /**
     * @return void
     */
    public function __set($name, $value)
    {
        $this->payData->offsetSet($name, $value);
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
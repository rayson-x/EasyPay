<?php
namespace EasyPay;

use BadMethodCallException;
use InvalidArgumentException;
use EasyPay\Interfaces\PayApiInterface;

/**
 * Class Pay
 * @package EasyPay
 *
 * @method initOrder()
 * @method orderQuery()
 * @method closeOrder()
 * @method refund()
 * @method refundQuery()
 * @method downloadBill()
 */
class Pay
{
    // 微信支付
    const WECHAT = 'wechat';

    // 阿里H5支付
    const ALI_WAP_PAY = 'ali-wap-pay';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \EasyPay\Interfaces\PayApiInterface
     */
    protected $instance;

    /**
     * 支持的支付方式列表
     *
     * @var array
     */
    protected $apiList = [
        'wechat'            => \EasyPay\PayApi\Wechat\PayApi::class,
        'ali-wap-pay'       => \EasyPay\PayApi\AliWapPay\PayApi::class,
    ];

    /**
     * @param array $config
     * @param $name
     */
    public function __construct(array $config, $name)
    {
        $this->config = $config;

        if (empty($className = $this->apiList[$name])) {
            throw new InvalidArgumentException('支付方式不支持');
        }

        $this->instance = (new $className($this->config));
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->instance, $method)) {
            throw new BadMethodCallException('方法不存在');
        }

        return $this->instance->$method(...$args);
    }

    /**
     * 设置配置信息
     *
     * @param $name
     * @param $value
     * @return $this
     */
    public function setConfig($name, $value)
    {
        $this->config[$name] = $value;

        return $this;
    }

    /**
     * @return \EasyPay\Interfaces\PayApiInterface
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @param Interfaces\PayApiInterface $instance
     */
    public function setInstance(PayApiInterface $instance)
    {
        $this->instance = $instance;
    }
}
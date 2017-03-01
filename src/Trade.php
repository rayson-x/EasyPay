<?php
namespace EasyPay;

use EasyPay\Exception\PayException;
use EasyPay\Interfaces\StrategyInterface;

/**
 * Class Trade
 * @package EasyPay
 */
class Trade
{
    /**
     * 策略集
     *
     * @var array
     */
    protected $strategyList = [
        'ali.qr.pay'            =>  \EasyPay\Strategy\Ali\QrPay::class,
        'ali.wap.pay'           =>  \EasyPay\Strategy\Ali\WapPay::class,
        'wechat.qr.pay'         =>  \EasyPay\Strategy\Wechat\QrPay::class,
        'wechat.pub.pay'        =>  \EasyPay\Strategy\Wechat\PubPay::class,
        'wechat.refund'         =>  \EasyPay\Strategy\Wechat\Refund::class,
        'wechat.query.order'    =>  \EasyPay\Strategy\Wechat\QueryOrder::class,
        'wechat.close.order'    =>  \EasyPay\Strategy\Wechat\CloseOrder::class,
        'wechat.refund.query'   =>  \EasyPay\Strategy\Wechat\RefundQuery::class,
    ];

    /**
     * @var StrategyInterface
     */
    protected $strategy;

    /**
     * @param $strategy
     * @param array $options
     */
    public function __construct($strategy, array $options = [])
    {
        $this->setStrategy($strategy, $options);
    }

    /**
     * 设置应用策略
     *
     * @param $strategy
     * @param array $options
     */
    public function setStrategy($strategy, array $options = [])
    {
        if (!array_key_exists($strategy, $this->strategyList)) {
            throw new \RuntimeException('操作不存在');
        }

        list($payment) = explode('.', $strategy);

        Config::loadConfig([$payment => $options]);

        $this->strategy = $this->strategyList[$strategy];
    }

    /**
     * @param array $payData
     * @return mixed
     */
    public function execute(array $payData = [])
    {
        if (is_string($this->strategy)) {
            $this->strategy = new $this->strategy($payData);
        }

        if (!$this->strategy instanceof StrategyInterface) {
            throw new PayException("支付方式不存在");
        }

        return $this->strategy->execute();
    }
}
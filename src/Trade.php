<?php
namespace EasyPay;

use EasyPay\Exception\PayException;
use EasyPay\Interfaces\StrategyInterface;

class Trade
{
    const ALI_WAP_PAY = \EasyPay\Strategy\Ali\WapPay::class;

    const WX_QR_PAY = \EasyPay\Strategy\Wechat\QrPay::class;

    const WX_PUB_PAY = \EasyPay\Strategy\Wechat\PubPay::class;

    const WX_QUERY_ORDER = \EasyPay\Strategy\Wechat\QueryOrder::class;

    const WX_CLOSE_ORDER = \EasyPay\Strategy\Wechat\CloseOrder::class;

    const WX_REFUND = \EasyPay\Strategy\Wechat\Refund::class;

    const WX_REFUND_QUERY = \EasyPay\Strategy\Wechat\RefundQuery::class;

    /**
     * @var StrategyInterface
     */
    protected $strategy;

    /**
     * @var array
     */
    protected $options;

    public function __construct($strategy, array $options = [])
    {
        if (!class_exists($strategy)) {
            throw new PayException("支付方式不存在");
        }

        $this->strategy = $strategy;
        $this->options = $options;
    }

    public function execute(array $payData = [])
    {
        if (is_string($this->strategy)) {
            $this->strategy = new $this->strategy(
                array_merge($this->options, $payData)
            );
        }

        if (!$this->strategy instanceof StrategyInterface) {
            throw new PayException("支付方式不存在");
        }

        return $this->strategy->execute();
    }
}
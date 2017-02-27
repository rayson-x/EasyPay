<?php
namespace EasyPay;

use EasyPay\Exception\PayException;
use EasyPay\Interfaces\StrategyInterface;

/**
 * Class Trade
 * @package EasyPay
 *
 * ali.wap.pay
 */
class Trade
{
    protected $strategyList = [
        'wechat.qr.pay'         =>  \EasyPay\Strategy\Wechat\QrPay::class,
        'wechat.pub.pay'        =>  \EasyPay\Strategy\Wechat\PubPay::class,
        'wechat.query.order'    =>  \EasyPay\Strategy\Wechat\QueryOrder::class,
        'wechat.close.order'    =>  \EasyPay\Strategy\Wechat\CloseOrder::class,
        'wechat.refund'         =>  \EasyPay\Strategy\Wechat\Refund::class,
        'wechat.refund.query'   =>  \EasyPay\Strategy\Wechat\RefundQuery::class,
        'ali.wap.pay'           =>  \EasyPay\Strategy\Ali\WapPay::class,
    ];

    /**
     * @var StrategyInterface
     */
    protected $strategy;

    public function __construct($strategy, array $options = [])
    {
        if (!array_key_exists($strategy, $this->strategyList)) {
            throw new \RuntimeException();
        }

        list($payment) = explode('.', $strategy);

        Config::loadConfig([$payment => $options]);

        $this->strategy = $this->strategyList[$strategy];
    }

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
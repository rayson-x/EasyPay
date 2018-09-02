<?php

namespace EasyPay;

use EasyPay\Interfaces\StrategyInterface;

class PayFactory
{
    /**
     * @var array
     */
    protected static $strategyList = [
        // 支付宝可用操作
        'ali.qr.pay'            =>  \EasyPay\Strategy\Ali\QrPay::class,
        'ali.wap.pay'           =>  \EasyPay\Strategy\Ali\WapPay::class,
        'ali.refund'            =>  \EasyPay\Strategy\Ali\Refund::class,
        'ali.transfers'         =>  \EasyPay\Strategy\Ali\Transfers::class,
        'ali.query.order'       =>  \EasyPay\Strategy\Ali\QueryOrder::class,
        'ali.close.order'       =>  \EasyPay\Strategy\Ali\CloseOrder::class,
        'ali.refund.query'      =>  \EasyPay\Strategy\Ali\RefundQuery::class,

        // 微信可用操作
        'wechat.qr.pay'         =>  \EasyPay\Strategy\Wechat\QrPay::class,
        'wechat.pub.pay'        =>  \EasyPay\Strategy\Wechat\PubPay::class,
        'wechat.app.pay'        =>  \EasyPay\Strategy\Wechat\AppPay::class,
        'wechat.wap.pay'        =>  \EasyPay\Strategy\Wechat\WapPay::class,
        'wechat.refund'         =>  \EasyPay\Strategy\Wechat\Refund::class,
        'wechat.transfers'      =>  \EasyPay\Strategy\Wechat\Transfers::class,
        'wechat.order.query'    =>  \EasyPay\Strategy\Wechat\QueryOrder::class,
        'wechat.order.close'    =>  \EasyPay\Strategy\Wechat\CloseOrder::class,
        'wechat.refund.query'   =>  \EasyPay\Strategy\Wechat\RefundQuery::class,
    ];

    /**
     * @param $strategy
     * @param array $options
     * @return StrategyInterface
     */
    public static function create($strategy, array $options = [])
    {
        if (!array_key_exists($strategy, self::$strategyList)) {
            throw new \RuntimeException('操作不存在');
        }

        $strategy = self::$strategyList[$strategy];

        $strategy = new $strategy($options);

        if (!$strategy instanceof StrategyInterface) {
            throw new \RuntimeException("错误的操作方式");
        }

        return $strategy;
    }
}
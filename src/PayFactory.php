<?php

namespace EasyPay;

use EasyPay\Interfaces\StrategyInterface;

class PayFactory
{
    /**
     * @var array
     */
    public static $provides = [
        // 支付宝可用操作
        'ali.qr.pay'            =>  \EasyPay\Strategies\Ali\QrPay::class,
        'ali.wap.pay'           =>  \EasyPay\Strategies\Ali\WapPay::class,
        'ali.refund'            =>  \EasyPay\Strategies\Ali\Refund::class,
        'ali.transfer'          =>  \EasyPay\Strategies\Ali\Transfer::class,
        'ali.query.order'       =>  \EasyPay\Strategies\Ali\QueryOrder::class,
        'ali.close.order'       =>  \EasyPay\Strategies\Ali\CloseOrder::class,
        'ali.refund.query'      =>  \EasyPay\Strategies\Ali\RefundQuery::class,

        // 微信可用操作
        'wechat.qr.pay'         =>  \EasyPay\Strategies\Wechat\QrPay::class,
        'wechat.pub.pay'        =>  \EasyPay\Strategies\Wechat\PubPay::class,
        'wechat.app.pay'        =>  \EasyPay\Strategies\Wechat\AppPay::class,
        'wechat.wap.pay'        =>  \EasyPay\Strategies\Wechat\WapPay::class,
        'wechat.refund'         =>  \EasyPay\Strategies\Wechat\Refund::class,
        'wechat.transfer'       =>  \EasyPay\Strategies\Wechat\Transfer::class,
        'wechat.order.query'    =>  \EasyPay\Strategies\Wechat\QueryOrder::class,
        'wechat.order.close'    =>  \EasyPay\Strategies\Wechat\CloseOrder::class,
        'wechat.refund.query'   =>  \EasyPay\Strategies\Wechat\RefundQuery::class,
    ];

    /**
     * @param $strategy
     * @param array $options
     * @return StrategyInterface
     */
    public static function create($strategy, array $options = [])
    {
        if (!array_key_exists($strategy, self::$provides)) {
            throw new \RuntimeException("[{$strategy}]服务不存在");
        }

        $strategy = self::$provides[$strategy];

        $service = new $strategy($options);

        if (!$service instanceof StrategyInterface) {
            throw new \RuntimeException("{$strategy}必须实现StrategyInterface");
        }

        return $service;
    }

    /**
     * @param string $name
     * @param StrategyInterface $strategy
     */
    public static function setProvide($name, StrategyInterface $strategy)
    {
        self::$provides[$name] = $strategy;
    }
}
<?php
namespace EasyPay;


class Notify
{
    /**
     * @var array
     */
    protected static $modes = [
        'ali'     =>  \EasyPay\Notify\Ali\Processor::class,
    ];

    /**
     * 获取异步结果处理器
     *
     * @param $mode
     * @return \EasyPay\Interfaces\AsyncNotifyProcessorInterface
     */
    public static function getProcessor($mode)
    {
        $class = isset(static::$modes[$mode]) ? static::$modes[$mode] : $mode;

        if (!class_exists($class)) {
            throw new \RuntimeException('通知处理器不存在');
        }

        return new $class;
    }
}
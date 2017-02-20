<?php
namespace EasyPay;


class AsyncNotify
{
    /**
     * @var array
     */
    protected static $modes = [
        'wechat' => \EasyPay\Notify\Wechat\AsyncProcessor::class,
    ];

    /**
     * 获取异步结果处理器
     *
     * @param $mode
     * @return \EasyPay\Interfaces\AsyncProcessorInterface
     */
    public static function getProcessor($mode)
    {
        $class = isset(static::$modes[$mode]) ? static::$modes[$mode] : $mode;

        return new $class;
    }

    /**
     * 注册处理异步回调函数
     *
     * @param callable $callback
     * @return self
     */
    public static function handle($mode, callable $callback)
    {
        $notify = static::getProcessor($mode);

        try {
            return $notify->success(
                call_user_func($callback, $notify->getNotify()) ?: 'OK'
            );
        } catch(\Exception $e) {
            return $notify->fail($e);
        }
    }
}
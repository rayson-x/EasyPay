<?php
namespace EasyPay;

class AsyncNotify extends Notify
{
    /**
     * @var array
     */
    protected static $modes = [
        'ali'     =>  \EasyPay\Notify\Ali\AsyncProcessor::class,
        'wechat'  =>  \EasyPay\Notify\Wechat\AsyncProcessor::class,
    ];

    /**
     * 注册处理异步回调函数
     *
     * @param string $mode
     * @param callable $callback
     * @return string
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
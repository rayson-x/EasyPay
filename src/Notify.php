<?php

namespace EasyPay;

use EasyPay\Interfaces\NotifyInterface;
use Illuminate\Http\Request as LaravelRequest;
use Psr\Http\Message\RequestInterface as PsrRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Notify
{
    public static $notifies = [
        'ali'     => \EasyPay\Notifies\Ali\Notify::class,
        'wechat'  => \EasyPay\Notifies\Wechat\Notify::class,    
    ];

    /**
     * @param $service
     * @param $options
     * @param $request
     * @return NotifyInterface
     */
    public static function get($service, $options = [], $request = null)
    {
        if (!is_null($request)) {
            return self::fromRequest($service, $request, $options);
        }

        $service = self::$notifies[$service];

        return $service::fromGlobal($options);
    }

    /**
     * @param $service
     * @param $request
     * @param $options
     * @return NotifyInterface
     */
    public static function fromRequest($service, $request, $options = [])
    {
        $service = self::$notifies[$service];

        switch ($request) {
            case $request instanceof PsrRequest:
                return $service::fromPsr7Request($request, $options);
            case $request instanceof LaravelRequest:
                return $service::fromLaravelRequest($request, $options);
            case $request instanceof SymfonyRequest:
                return $service::fromSymfonyRequest($request, $options);
        }

        throw new \RuntimeException('无法处理的请求');
    }

    /**
     * @param $service
     * @param $callback
     * @param $request
     * @return string
     */
    public static function handle(
        $service,
        callable $callback, 
        $options = [],
        $request = null
    ) {
        $notify = self::get($service, $options, $request);

        try {
            return $notify->success($callback($notify));
        } catch (\Exception $e) {
            return $notify->fail($e->getMessage());
        } catch (\Throwable $e) {
            return $notify->fail($e->getMessage());
        }
    }
}

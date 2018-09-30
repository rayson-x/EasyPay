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
     * @param $request
     * @return NotifyInterface
     */
    public static function get($service, $request = null)
    {
        $service = self::$notifies[$service];

        if (is_null($request)) {
            return $service::fromGlobal();
        }

        switch ($request) {
            case $request instanceof PsrRequest:
                return $service::fromPsr7Request($request);
            case $request instanceof LaravelRequest:
                return $service::fromLaravelRequest($request);
            case $request instanceof SymfonyRequest:
                return $service::fromSymfonyRequest($request);
        }

        throw new \RuntimeException('无法处理的请求');
    }

    /**
     * @param $service
     * @param $callback
     * @param $request
     * @return string
     */
    public static function handle($service, callable $callback, $request = null)
    {
        $notify = self::get($service, $request);

        try {
            return $notify->success($callback($notify));
        } catch (\Exception $e) {
            return $notify->fail($e->getMessage());
        } catch (\Throwable $e) {
            return $notify->fail($e->getMessage());
        }
    }
}

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
}

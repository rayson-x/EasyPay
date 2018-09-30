<?php
namespace EasyPay\Interfaces;

use ArrayAccess;
use Psr\Http\Message\RequestInterface;
use Illuminate\Http\Request as LaravelRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * 交易相关接口
 *
 * Interface NotifyInterface
 * @package EasyPay\Interfaces
 */
interface NotifyInterface extends ArrayAccess
{
    public static function fromGlobal();
    
    public static function fromSymfonyRequest(SymfonyRequest $request);

    public static function fromLaravelRequest(LaravelRequest $request);

    public static function fromPsr7Request(RequestInterface $request);
}
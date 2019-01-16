<?php 

namespace EasyPay\Notifies\Wechat;

use RuntimeException;
use EasyPay\Notifies\BaseNotify;
use Psr\Http\Message\RequestInterface;
use EasyPay\TradeData\Wechat\TradeData;
use Illuminate\Http\Request as LaravelRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Notify extends BaseNotify
{
    /**
     * {@inheritDoc}
     */
    public static function fromGlobal($options = [])
    {
        if (in_array(PHP_SAPI, self::$badModes)) {
            throw new RuntimeException('运行模式错误,必须运行在指定的模式下');
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? null;

        switch ($method) {
            case "POST" :
                $input = file_get_contents('php://input');

                return new self(TradeData::createFromXML($input, $options));
            default :
                return new self(new TradeData($_REQUEST, $options));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function fromSymfonyRequest(SymfonyRequest $request, $options = [])
    {
        if ($request->getMethod() !== 'POST') {
            throw new RuntimeException('无法处理的请求');
        }

        return new self(TradeData::createFromXML($request->getContent(), $options));
    }

    /**
     * {@inheritDoc}
     */
    public static function fromLaravelRequest(LaravelRequest $request, $options = [])
    {
        if ($request->getMethod() !== 'POST') {
            throw new RuntimeException('无法处理的请求');
        }

        return new self(TradeData::createFromXML($request->getContent(), $options));
    }

    /**
     * {@inheritDoc}
     */
    public static function fromPsr7Request(RequestInterface $request, $options = [])
    {
        $method = $request->getMethod();

        if ($method !== 'POST') {
            throw new RuntimeException('无法处理的请求');
        }

        return new self(TradeData::createFromXML((string) $request->getBody(), $options));
    }

    /**
     * {@inheritDoc}
     */
    public function __construct(TradeData $tradeData)
    {
        $this->tradeData = $tradeData;

        $this->verifySign();
    }
    
    /**
     * {@inheritDoc}
     */
    protected function verifySign()
    {
        $this->tradeData->verifySign();
    }

    /**
     * {@inheritDoc}
     */
    public function success($message = "OK")
    {
        return (new TradeData([
            'return_code' => 'SUCCESS',
            'return_msg'  => $message
        ]))->toXml();
    }

    /**
     * {@inheritDoc}
     */
    public function fail($message = "ERROR")
    {
        return (new TradeData([
            'return_code' => 'FAIL',
            'return_msg'  => $message
        ]))->toXml();
    }
}
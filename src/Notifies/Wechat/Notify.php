<?php 

namespace EasyPay\Notifies\Wechat;

use RuntimeException;
use EasyPay\Notifies\BaseNoitfy;
use Psr\Http\Message\RequestInterface;
use EasyPay\TradeData\Wechat\TradeData;
use Illuminate\Http\Request as LaravelRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class Notify extends BaseNoitfy
{
    public static function fromGlobal()
    {
        if (substr(PHP_SAPI, 0, 3) !== 'cgi') {
            throw new RuntimeException('必须运行在cgi模式下');
        }

        $method = $_SERVER['REQUEST_METHOD'] ?? null;

        switch ($method) {
            case "POST" :
                $input = file_get_contents('php://input');

                return new self(TradeData::createFromXML($input));
            default :
                return new self(new TradeData($_REQUEST));
        }
    }
    
    public static function fromSymfonyRequest(SymfonyRequest $request)
    {
        $method = $request->getMethod();

        switch ($method) {
            case "POST" :
                $input = $request->getContent();

                return new self(TradeData::createFromXML($input));
            default :
                throw new RuntimeException('无法处理的请求');
        }
    }

    public static function fromLaravelRequest(LaravelRequest $request)
    {
        return new self(new TradeData($request->input()));
    }

    public static function fromPsr7Request(RequestInterface $request)
    {
        $method = $request->getMethod();

        switch ($method) {
            case "POST" :
                $input = (string) $request->getBody();
            default :
                throw new RuntimeException('无法处理的请求');
        }

        return new self(TradeData::createFromXML($input));
    }

    protected function verifySign()
    {
        $this->tradeData->verifySign();
    }
}
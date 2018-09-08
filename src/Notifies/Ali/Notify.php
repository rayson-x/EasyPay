<?php 

namespace EasyPay\Notifies\Ali;

use RuntimeException;
use EasyPay\Notifies\BaseNoitfy;
use EasyPay\TradeData\Ali\TradeData;
use Psr\Http\Message\RequestInterface;
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
            case "GET" :
                return new self(new TradeData($_GET));
            case "POST" :
                return new self(new TradeData($_POST));
            default :
                return new self(new TradeData($_REQUEST));
        }
    }
    
    public static function fromSymfonyRequest(SymfonyRequest $request)
    {
        $method = $request->getMethod();

        switch ($method) {
            case "GET" :
                return new self(new TradeData($request->query->all()));
            case "POST" :
                return new self(new TradeData($request->request->all()));
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
            case "GET" :
                $input = $request->getUri()->getQuery();
            case "POST" :
                $input = (string) $request->getBody();
            default :
                throw new RuntimeException('无法处理的请求');
        }

        parse_str($input, $result);

        return new self(new TradeData($result));
    }

    public function __construct(TradeData $tradeData)
    {
        $this->tradeData = $tradeData;

        $this->verifySign();
    }

    protected function verifySign()
    {
        $this->tradeData->verifyRequestSign();
    }
}
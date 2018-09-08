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
    /**
     * {@inheritDoc}
     */
    public static function fromGlobal()
    {
        if (in_array(PHP_SAPI, self::$badModes)) {
            throw new RuntimeException('运行模式错误,必须运行在指定的模式下');
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
    
    /**
     * {@inheritDoc}
     */
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
    
    /**
     * {@inheritDoc}
     */
    public static function fromLaravelRequest(LaravelRequest $request)
    {
        return new self(new TradeData($request->input()));
    }
    
    /**
     * {@inheritDoc}
     */
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
    
    /**
     * {@inheritDoc}
     */
    protected function verifySign()
    {
        $this->tradeData->verifyRequestSign();
    }
}
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
            case "POST" :
                $input = file_get_contents('php://input');

                return new self(TradeData::createFromXML($input));
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
            case "POST" :
                $input = $request->getContent();

                return new self(TradeData::createFromXML($input));
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
            case "POST" :
                $input = (string) $request->getBody();
            default :
                throw new RuntimeException('无法处理的请求');
        }

        return new self(TradeData::createFromXML($input));
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
        $this->tradeData->verifySign();
    }
}
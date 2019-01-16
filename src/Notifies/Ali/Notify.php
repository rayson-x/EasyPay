<?php 

namespace EasyPay\Notifies\Ali;

use RuntimeException;
use EasyPay\Notifies\BaseNotify;
use EasyPay\TradeData\Ali\TradeData;
use Psr\Http\Message\RequestInterface;
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
            case "GET" :
                return new self(new TradeData($_GET, $options));
            case "POST" :
                return new self(new TradeData($_POST, $options));
            default :
                return new self(new TradeData($_REQUEST, $options));
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function fromSymfonyRequest(SymfonyRequest $request, $options = [])
    {
        $method = $request->getMethod();

        switch ($method) {
            case "GET" :
                $input = $request->query->all();
                break;
            case "POST" :
                $input = $request->request->all();
                break;
            default :
                throw new RuntimeException('无法处理的请求');
        }

        return new self(new TradeData($input, $options));
    }

    /**
     * {@inheritDoc}
     */
    public static function fromLaravelRequest(LaravelRequest $request, $options = [])
    {
        return new self(new TradeData($request->input(), $options));
    }

    /**
     * {@inheritDoc}
     */
    public static function fromPsr7Request(RequestInterface $request, $options = [])
    {
        $method = $request->getMethod();

        switch ($method) {
            case "GET" :
                $input = $request->getUri()->getQuery();
                break;
            case "POST" :
                $input = (string) $request->getBody();
                break;
            default :
                throw new RuntimeException('无法处理的请求');
        }

        parse_str($input, $result);

        return new self(new TradeData($result, $options));
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
        $this->tradeData->verifyRequestSign();
    }

    /**
     * {@inheritDoc}
     */
    public function success($message = "OK")
    {
        return 'success';
    }

    /**
     * {@inheritDoc}
     */
    public function fail($message = "ERROR")
    {
        return 'fail';
    }
}
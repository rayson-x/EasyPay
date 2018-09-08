<?php
namespace EasyPay\Notify\Wechat;

use Exception;
use RuntimeException;
use EasyPay\TradeData\Wechat\TradeData;
use EasyPay\Interfaces\AsyncNotifyProcessorInterface;

/**
 * 异步通知处理器
 *
 * Class AsyncProcessor
 * @package EasyPay\Notify\Wechat
 */
class AsyncProcessor implements AsyncNotifyProcessorInterface
{
    /**
     * 获取通知内容
     *
     * @param null $request
     * @return TradeData
     * @throws Exception
     */
    public function getNotify($request = null)
    {
        if (!is_null($request)) {
            if (method_exists($request, 'getMethod')) {
                $method = $request->getMethod();
            }

            if (method_exists($request, 'getBody')) {
                // psr-7
                $input = (string) $request->getBody();
            } elseif (method_exists($request, 'getContent')) {
                // laravel or symfony
                $input = (string) $request->getContent();
            }
        } elseif (substr(PHP_SAPI, 0, 3) == 'cgi') {
            $method = $_SERVER['REQUEST_METHOD'];
            // 从输入流中读取数据
            $input = file_get_contents("php://input");            
        }
        
        if (empty($method) || $method !== 'POST') {
            throw new RuntimeException('无法处理的请求');
        }

        $data = TradeData::createFromXML($input);
        $data->verifySign();

        return $data;
    }

    /**
     * @param string $result
     * @return string
     */
    public function success($result = 'OK')
    {
        return (new TradeData([
            'return_code'   => 'SUCCESS' ,
            'return_msg'    => $result
        ]))->toXml();
    }

    /**
     * @param Exception $exception
     * @return string
     */
    public function fail(Exception $exception)
    {
        return (new TradeData([
            'return_code'   => 'FAIL' ,
            'return_msg'    => $exception->getMessage()
        ]))->toXml();
    }
}
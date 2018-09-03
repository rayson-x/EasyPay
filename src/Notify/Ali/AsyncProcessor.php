<?php
namespace EasyPay\Notify\Ali;

use Exception;
use RuntimeException;
use EasyPay\TradeData\Ali\TradeData;
use EasyPay\Interfaces\AsyncNotifyProcessorInterface;

class AsyncProcessor implements AsyncNotifyProcessorInterface
{
    /**
     * 获取通知内容
     *
     * @param null $request
     * @return TradeData
     * @throws \Exception
     */
    public function getNotify($request = null)
    {
        if (!is_null($request)) {
            if (method_exists($request, 'getMethod')) {
                $method = $request->getMethod();
            }

            if (method_exists($request, 'input')) {
                // laravel
                $input = $request->input();
            } elseif (property_exists($request, 'request')) {
                // symfony
                $input = $request->request->all();
            }
        } elseif (substr(PHP_SAPI, 0, 3) == 'cgi') {
            $method = $_SERVER['REQUEST_METHOD'];
            // 从输入流中读取数据
            $input = $_POST;
        }
        
        if (empty($method) || $method !== 'POST') {
            throw new RuntimeException('无法处理的请求');
        }

        $data = new TradeData($input);
        $data->verifyRequestSign();

        return $data;
    }

    /**
     * 异步信息处理成功
     *
     * @param $result
     * @return string
     */
    public function success($result = null)
    {
        return "success";
    }

    /**
     * 异步信息处理时出现异常
     *
     * @param \Exception $exception
     * @return string
     */
    public function fail(Exception $exception)
    {
        return 'fail';
    }
}
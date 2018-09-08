<?php
namespace EasyPay\Notify\Ali;

use EasyPay\TradeData\Ali\TradeData;
use EasyPay\Interfaces\NotifyProcessorInterface;

class Processor implements NotifyProcessorInterface
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
            } elseif (property_exists($request, 'query')) {
                // symfony
                $input = $request->query->all();
            }
        } elseif (substr(PHP_SAPI, 0, 3) == 'cgi') {
            $method = $_SERVER['REQUEST_METHOD'];
            // 从输入流中读取数据
            $input = $_GET;            
        }
        
        if (empty($method) || $method !== 'GET') {
            throw new RuntimeException('无法处理的请求');
        }

        $data = new TradeData($input);
        $data->verifyRequestSign();

        return $data;
    }
}
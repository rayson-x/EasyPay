<?php
namespace EasyPay\Notify\Wechat;

use Exception;
use EasyPay\DataManager\Wechat\Data;
use EasyPay\Interfaces\AsyncNotifyProcessorInterface;

/**
 * 异步通知处理器
 *
 * Class AsyncProcessor
 * @package EasyPay\Strategy\Notify
 */
class AsyncProcessor implements  AsyncNotifyProcessorInterface
{
    /**
     * 获取通知内容
     *
     * @return Data
     * @throws Exception
     */
    public function getNotify()
    {
        if (empty($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('无法处理的请求');
        }

        // 从输入流中读取数据
        $input = file_get_contents("php://input");
        $data = Data::createDataFromXML($input);
        $data->verifySign();

        return $data;
    }

    /**
     * @param string $result
     * @return string
     */
    public function success($result = 'OK')
    {
        return $this->replyNotify([
            'return_code' => 'SUCCESS' ,
            'return_msg' => $result
        ]);
    }

    /**
     * @param Exception $exception
     * @return string
     */
    public function fail(Exception $exception)
    {
        return $this->replyNotify([
            'return_code' => 'FAIL' ,
            'return_msg' => $exception->getMessage()
        ]);
    }

    /**
     * 获取异步通知的响应内容
     *
     * @param $message
     * @return string
     */
    public function replyNotify($message)
    {
        $res = new Data($message);

        return $res->toXml();
    }
}
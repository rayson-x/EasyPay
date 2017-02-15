<?php
namespace EasyPay\PayApi\Wechat;

use Exception;
use EasyPay\Interfaces\AsyncProcessorInterface;

/**
 * 处理异步通知
 *
 * Class AsyncNotifyHandle
 * @package EasyPay\PayApi\Wechat
 */
class AsyncProcessor implements AsyncProcessorInterface
{
    /**
     * 获取通知内容
     *
     * @return PayData
     * @throws Exception
     */
    public function getNotify()
    {
        if (empty($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('无法处理的请求');
        }
        // 从输入流中读取数据
        $input = file_get_contents("php://input");
        $body = PayData::createDataFromXML($input);
        $body->checkResult();

        return $body;
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
        $res = new PayData($message);

        return $res->toXml();
    }
}
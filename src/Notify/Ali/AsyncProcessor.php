<?php
namespace EasyPay\Notify\Ali;

use EasyPay\DataManager\Ali\Data;
use EasyPay\Interfaces\AsyncNotifyProcessorInterface;

class AsyncProcessor implements AsyncNotifyProcessorInterface
{
    /**
     * 获取通知内容
     *
     * @return Data
     * @throws \Exception
     */
    public function getNotify()
    {
        if (empty($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new \Exception('无法处理的请求');
        }

        $data = new Data($_POST);
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
    public function fail(\Exception $exception)
    {
        return 'fail';
    }
}
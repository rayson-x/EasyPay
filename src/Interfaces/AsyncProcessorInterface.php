<?php
namespace EasyPay\Interfaces;

/**
 * Interface AsyncNotifyInterface
 * @package EasyPay\Interfaces
 */
interface AsyncProcessorInterface
{
    /**
     * 获取异步通知信息
     *
     * @return array|object
     */
    public function getNotify();

    /**
     * 异步信息处理成功
     *
     * @param $result
     */
    public function success($result = null);

    /**
     * 异步信息处理时出现异常
     *
     * @param \Exception $exception
     */
    public function fail(\Exception $exception);

    /**
     * 获取异步通知的响应内容
     *
     * @param $message
     */
    public function replyNotify($message);
}
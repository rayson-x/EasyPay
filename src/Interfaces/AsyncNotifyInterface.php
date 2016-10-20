<?php
namespace EasyPay\Interfaces;

/**
 * Interface AsyncNotifyInterface
 * @package EasyPay\Interfaces
 */
interface AsyncNotifyInterface
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
    public function success($result);

    /**
     * 异步信息处理时出现异常
     *
     * @param \Exception $exception
     */
    public function fail(\Exception $exception);

    /**
     * 响应通知方服务器
     */
    public function replyNotify();
}
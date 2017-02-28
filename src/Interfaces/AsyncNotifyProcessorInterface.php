<?php
namespace EasyPay\Interfaces;

/**
 * Interface AsyncNotifyProcessorInterface
 * @package EasyPay\Interfaces
 */
interface AsyncNotifyProcessorInterface extends NotifyProcessorInterface
{
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
}
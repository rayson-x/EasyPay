<?php
namespace EasyPay\Interfaces;

/**
 * Interface AsyncNotifyProcessorInterface
 * @package EasyPay\Interfaces
 */
interface AsyncNotifyProcessorInterface extends NotifyProcessorInterface
{
    /**
     * 成功回调
     *
     * @param $result
     */
    public function success($result = null);

    /**
     * 处理失败信息
     *
     * @param \Exception $exception
     */
    public function fail(\Exception $exception);
}
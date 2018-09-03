<?php

namespace EasyPay\Interfaces;

/**
 * Interface AsyncNotifyInterface
 * @package EasyPay\Interfaces
 */
interface NotifyProcessorInterface
{
    /**
     * 获取通知信息
     *
     * @param null|object $request
     * @return array|object
     */
    public function getNotify($request = null);
}
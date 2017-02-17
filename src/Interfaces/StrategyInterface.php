<?php
namespace EasyPay\Interfaces;

/**
 * Interface StrategyInterface
 * @package EasyPay\Interfaces
 */
interface StrategyInterface
{
    /**
     * 执行具体业务逻辑
     *
     * @return mixed
     */
    public function execute();
}
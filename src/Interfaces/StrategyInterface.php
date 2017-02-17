<?php
namespace EasyPay\Interfaces;

/**
 * Todo 交易数据构造策略集
 *
 * 交易相关策略接口
 *
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
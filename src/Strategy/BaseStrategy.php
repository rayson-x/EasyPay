<?php
namespace EasyPay\Strategy;

use EasyPay\Interfaces\StrategyInterface;

abstract class BaseStrategy implements StrategyInterface
{
    /**
     * 生成数据
     *
     * @return mixed
     */
    abstract protected function buildData();

    /**
     * 获取请求的Http动词
     *
     * @return mixed
     */
    abstract protected function getRequestMethod();

    /**
     * 获取请求的目标
     *
     * @return string
     */
    abstract protected function getRequestTarget();

    /**
     * 处理接口返回结果
     *
     * @param $result
     * @return mixed
     */
    abstract protected function handleResult($result);
}
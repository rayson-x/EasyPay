<?php
namespace EasyPay\Exception;

use RuntimeException;

/**
 * 支付过程出现异常
 *
 * Class PayException
 * @package PayApi\Exception
 */
class PayException extends RuntimeException
{
    /**
     * @var object|array
     */
    protected $payResult;

    /**
     * 新建一个支付异常
     *
     * @param $result
     * @param string $message
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($result, $message = '', $code = 0, \Exception $previous = null)
    {
        $this->payResult = $result;

        parent::__construct($message,$code);
    }

    /**
     * 返回结果集
     *
     * @return object|array
     */
    public function getResult()
    {
        return $this->payResult;
    }
}
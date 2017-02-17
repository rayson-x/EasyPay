<?php
namespace EasyPay\Exception;

/**
 * Class PayFailException
 * @package PayApi\Exception
 */
class PayFailException extends PayException
{
    /**
     * @var object|array
     */
    protected $err_code;

    /**
     * 新建一个支付异常
     *
     * @param $result
     * @param string $message
     * @param int|string $code
     * @param \Exception|null $previous
     */
    public function __construct($result, $message = '', $code = 0, \Exception $previous = null)
    {
        $this->err_code = $code;

        parent::__construct($result,$message,(int) $code,$previous);
    }

    /**
     * 返回错误码
     *
     * @return object|array
     */
    public function getErrCode()
    {
        return $this->err_code;
    }
}
<?php
namespace EasyPay\PayApi\Wechat;

use Exception;
use EasyPay\Exception\PayException;
use EasyPay\Interfaces\AsyncNotifyInterface;
use EasyPay\Exception\SignVerifyFailException;

/**
 * 处理异步通知
 *
 * Class AsyncNotifyHandle
 * @package EasyPay\PayApi\Wechat
 */
class AsyncNotify implements AsyncNotifyInterface
{
    protected $message = [];

    /**
     * @return PayData
     * @throws Exception
     */
    public function getNotify()
    {
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            throw new Exception('无法处理的请求');
        }

        $body = null;
        // 先从POST变量中提取数据
        if(in_array($_SERVER['CONTENT_TYPE'],['application/x-www-form-urlencoded','multipart/form-data'])){
            $body = new PayData($_POST);
        }

        // 从输入流中读取数据
        if(!$body){
            $body = file_get_contents("php://input");
            $body = PayData::createDataFromXML($body);
        }

        $body->checkResult();

        return $body;
    }

    /**
     * @param string $result
     */
    public function success($result = 'OK')
    {
        $this->message = [
            'return_code' => 'SUCCESS' ,
            'return_msg' => $result
        ];
    }

    /**
     * @param Exception $exception
     */
    public function fail(Exception $exception)
    {
        if($exception instanceof SignVerifyFailException){
            $message = '签名验证失败';
        }elseif($exception instanceof PayException){
            $message = "结果返回失败";
        }else{
            $message = $exception->getMessage();
        }

        $this->message = [
            'return_code' => 'FAIL' ,
            'return_msg' => $message
        ];
    }

    /**
     * 响应第三方
     */
    public function replyNotify()
    {
        $res = new PayData($this->message);

        echo $res->toXml();
    }
}
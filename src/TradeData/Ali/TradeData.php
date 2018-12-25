<?php
namespace EasyPay\TradeData\Ali;

use RuntimeException;
use EasyPay\Exception\PayException;
use EasyPay\TradeData\BaseTradeData;
use EasyPay\Exception\PayParamException;
use EasyPay\Exception\SignVerifyFailException;

/**
 * Class TradeData
 * @package EasyPay\TradeData\Ali
 */
class TradeData extends BaseTradeData
{
    /**
     * 构造签名
     *
     * @return mixed
     */
    public function makeSign()
    {
        // ali 加密必须要证书
        if (!$sslPrivate = $this->getOption('ssl_private_key')) {
            throw new RuntimeException("加密签名需要私钥证书,请检查配置");
        }

        // 获取加密方式
        $signType = $this->getSignType();

        $sign = (new \EasyPay\Utils\Rsa())
            ->setPrivateKey($sslPrivate)
            ->sign($this->buildData(), $signType);

        return base64_encode($sign);
    }

    /**
     * 支付宝签名验证模式一
     * 用于异步通知的签名验证
     */
    public function verifyRequestSign()
    {
        $data = $this->original;
        // 将待验签以外字段清除
        array_forget($data, ['sign', 'sign_type']);
        // 将Key以Ascii表进行排序
        ksort($data);
        // 生成查询参数
        $dataStr = urldecode(http_build_query($data));
        // 验证签名是否正确
        $this->verifySign($dataStr, $this->sign);
    }

    /**
     * 支付宝签名验证模式二
     * 用于验证是否为支付宝服务端响应
     *
     * Response body数据格式
     * {
     *     "*_response" : {"code": "10000", "msg": "Success", .....},
     *     "sign" : "MT5kbWb+oFSbcTyPsUPgoq7qdhqTotz+gVe....."
     * }
     */
    public function verifyResponseSign()
    {
        // 取出支付宝返回数据与签名
        list($message, $sign) = array_values($this->original);
        // 验证请求是否成功
        if ($message['code'] != '10000') {
            throw new PayException($this, $message['sub_msg']);
        }
        // 验证签名是否正确
        $this->verifySign(json_encode($message), $sign);
        // 将 *_response 中的内容合并,同时保留 *_response
        $this->replace($message);
    }

    /**
     * 构造公共参数
     *
     * @return string
     */
    protected function buildData()
    {
        // 构造时间戳
        if (!$this->offsetExists('timestamp')) {
            $this->timestamp = date('Y-m-d H:i:s');
        }
        // 构造请求参数
        if ($this->offsetExists('biz_content') && !is_string($this->biz_content)) {
            $this->biz_content = json_encode($this->biz_content,JSON_UNESCAPED_UNICODE);
        }

        $data = $this->toArray();
        // 排除 sign 字段
        array_forget($data, ['sign']);
        // 清空数组的空数据
        array_removal_empty($data);
        // 将Key以Ascii表进行排序
        ksort($data);

        return urldecode(http_build_query($data));
    }

    /**
     * 验证签名
     *
     * @param $message
     * @param $sign
     */
    protected function verifySign($message, $sign)
    {
        // ali 验证签名
        if (!$sslPublicKey = $this->getOption('ali_public_key')) {
            throw new PayParamException("验证签名需要公钥证书,请检查配置");
        }
        // 获取加密方式
        $signType = $this->getSignType();
        // 获取RSA加密解密对象
        $rsa = (new \EasyPay\Utils\Rsa())->setPublicKey($sslPublicKey);
        // 验证签名是否正确
        if (!$rsa->validate($message, base64_decode($sign), $signType)) {
            throw new SignVerifyFailException($this, '支付宝签名校验失败');
        }
    }

    /**
     * 获取加密方式
     *
     * @return int
     */
    protected function getSignType()
    {
        // 获取加密方式
        $type = $this->offsetExists('sign_type')
            ? strtoupper($this->sign_type)
            : $this->getOption('sign_type');

        switch ($type) {
            case "RSA" :
                return OPENSSL_ALGO_SHA1;
            case "RSA2" :
                return OPENSSL_ALGO_SHA256;
            default :
                throw new PayParamException("签名类型错误");
        }
    }
}
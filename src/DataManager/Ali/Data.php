<?php
namespace EasyPay\DataManager\Ali;

use EasyPay\Config;
use Ant\Support\Arr;
use EasyPay\Exception\PayException;
use EasyPay\DataManager\BaseDataManager;
use EasyPay\Exception\SignVerifyFailException;

class Data extends BaseDataManager
{
    /**
     * 构造签名
     *
     * @return mixed
     */
    public function makeSign()
    {
        // ali 加密必须要证书
        if (!$sslPath = Config::ali('ssl_private_key')) {
            throw new \RuntimeException("加密签名需要私钥证书,请检查配置");
        }

        // 获取加密方式
        $signType = $this->getSignType();

        $sign = (new \EasyPay\Utils\Rsa())
            ->setPrivateKey($sslPath)
            ->sign($this->buildData(), $signType);

        return base64_encode($sign);
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
        Arr::forget($data, ['sign']);
        Arr::removalEmpty($data);

        // 将Key以Ascii表进行排序
        ksort($data);

        return urldecode(http_build_query($data));
    }

    /**
     * 验证签名是否一致
     */
    public function verifySign()
    {
        // ali 验证签名
        if (!$sslPath = Config::ali('ali_public_key')) {
            throw new \RuntimeException("验证签名需要公钥证书,请检查配置");
        }

        $data = $this->toArray();
        // 获取加密方式
        $signType = $this->getSignType();
        // 将待验签以外字段清除
        Arr::forget($data, ['sign', 'sign_type']);
        // 将Key以Ascii表进行排序
        ksort($data);
        // 生成查询参数
        $dataStr = urldecode(http_build_query($data));
        // 获取RSA加密解密对象
        $rsa = (new \EasyPay\Utils\Rsa())->setPublicKey($sslPath);

        if (!$rsa->validate($dataStr, base64_decode($this->sign), $signType)) {
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
            : 'RSA';

        switch ($type) {
            case "RSA" :
                return OPENSSL_ALGO_SHA1;
                break;
            case "RSA2" :
                return OPENSSL_ALGO_SHA256;
                break;
            default :
                throw new PayException("签名类型错误");
        }
    }
}
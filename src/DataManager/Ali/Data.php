<?php
namespace EasyPay\DataManager\Ali;

use EasyPay\Config;
use Ant\Support\Arr;
use EasyPay\Exception\PayException;
use EasyPay\DataManager\BaseDataManager;

class Data extends BaseDataManager
{
    protected function getOption($name)
    {
        return Config::ali($name);
    }

    public function makeSign()
    {
        $signType = $this->offsetExists('sign_type') ? $this->sign_type : 'RSA';

        switch ($signType) {
            case "RSA" :
                $sign = (new \EasyPay\Utils\Rsa())
                    ->setEncryptCallback('base64_encode')
                    ->setPrivateKey($this->getOption('ssl_private_key'))
                    ->sign($this->buildData());
                break;
            case "RSA2" :
                $sign = (new \EasyPay\Utils\Rsa())
                    ->setEncryptCallback('base64_encode')
                    ->setPrivateKey($this->getOption('ssl_private_key'))
                    ->sign($this->buildData(), OPENSSL_ALGO_SHA256);
                break;
            default :
                throw new PayException("签名类型错误");
        }

        return $sign;
    }

    // 构造公共参数
    protected function buildData()
    {
        Arr::forget($this->items, ['sign']);
        $this->removalEmpty($this->items);
        // 构造请求参数
        if ($this->offsetExists('biz_content') && !is_string($this->biz_content)) {
            $this->biz_content = json_encode($this->biz_content,JSON_UNESCAPED_UNICODE);
        }

        // 将Key以Ascii表进行排序
        ksort($this->items);

        return urldecode(http_build_query($this->items));
    }
}
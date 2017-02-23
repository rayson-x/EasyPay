<?php
namespace EasyPay\DataManager\Ali;

use EasyPay\Config;
use Ant\Support\Arr;
use EasyPay\Exception\PayException;
use EasyPay\DataManager\BaseDataManager;

class Data extends BaseDataManager
{
    protected $data = [
        'format'        =>  'JSON',
        'charset'       =>  'UTF-8',
        'sign_type'     =>  'RSA',
        'version'       =>  '1.0',
        'product_code'  =>  'QUICK_WAP_PAY'
    ];

    /**
     * 构造签名
     *
     * @return mixed
     */
    public function makeSign()
    {
        // ali 加密必须要证书
        if (!$sslPath = Config::ali('ssl_private_key')) {
            throw new PayException("加密签名需要私钥证书证书,请检查配置");
        }

        // 获取加密方式
        $type = $this->offsetExists('sign_type')
            ? $this->sign_type
            : 'RSA';

        switch ($type) {
            case "RSA" :
                $signType = OPENSSL_ALGO_SHA1;
                break;
            case "RSA2" :
                $signType = OPENSSL_ALGO_SHA256;
                break;
            default :
                throw new PayException("签名类型错误");
        }

        $sign = (new \EasyPay\Utils\Rsa())
            ->setPrivateKey($sslPath)
            ->sign($this->buildData(), $signType);

        return base64_encode($sign);
    }

    // 构造公共参数
    protected function buildData()
    {
        $this->timestamp = date('Y-m-d H:i:s');
        // 构造请求参数
        if ($this->offsetExists('biz_content') && !is_string($this->biz_content)) {
            $this->biz_content = json_encode($this->biz_content,JSON_UNESCAPED_UNICODE);
        }

        $data = $this->toArray();
        Arr::forget($data, ['sign']);
        $this->removalEmpty($data);

        // 将Key以Ascii表进行排序
        ksort($data);

        return urldecode(http_build_query($data));
    }
}
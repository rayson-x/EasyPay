<?php
namespace EasyPay\DataManager\Wechat;

use DOMDocument;
use EasyPay\Config;
use Ant\Support\Arr;
use EasyPay\Exception\PayException;
use EasyPay\Exception\PayParamException;
use EasyPay\DataManager\BaseDataManager;
use EasyPay\Exception\SignVerifyFailException;

/**
 * Class Data
 * @package EasyPay\Strategy\Wechat
 */
class Data extends BaseDataManager
{
    /**
     * 生成CDATA格式的XML
     *
     * @return string
     */
    public function toXml()
    {
        $dom = new DOMDocument();
        $xml = $dom->createElement('xml');

        foreach ($this as $key => $value) {
            $item = $dom->createElement($key);
            $item->appendChild($dom->createCDATASection($value));
            $xml->appendChild($item);
        }

        $dom->appendChild($xml);
        return $dom->saveXML();
    }

    /**
     * 设置签名
     */
    public function setSign()
    {
        if (!$this->offsetExists('nonce_str')) {
            $this['nonce_str'] = $this->createNonceStr();
        }

        $this['sign'] = $this->makeSign();
    }

    /**
     * 生成签名(每次都重新生成,确保是最新参数生成的签名)
     *
     * @return string
     * @see https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
     */
    public function makeSign()
    {
        // 默认使用MD5加密
        $signType = $this->offsetExists('sign_type') ? $this->sign_type : "MD5";

        switch ($signType) {
            case 'MD5':
                $result = md5($this->buildQuery());
                break;
            default:
                throw new PayException("签名类型错误");
        }

        return strtoupper($result);
    }

    /**
     * 生成URL参数
     *
     * @return string
     */
    protected function buildQuery()
    {
        $data = $this->toArray();
        // 删除签名与key
        Arr::forget($data, ['sign','key']);
        // 删除空数据
        $this->removalEmpty($data);
        // 将Key以Ascii表进行排序
        ksort($data);

        if (!$data['key'] = Config::wechat('key')) {
            throw new PayParamException('商户支付密钥不存在,请检查参数');
        }

        // 构造完成后,使用urldecode进行解码
        return urldecode(http_build_query($data));
    }

    /**
     * 验证签名是否一致
     */
    public function verifySign()
    {
        if (!$this->offsetExists('sign') || $this->sign != $this->makeSign()) {
            throw new SignVerifyFailException($this, '签名校验失败');
        }
    }

    /**
     * 检查结果是否成功
     *
     * @param $code
     * @return bool
     */
    public function isSuccess($code)
    {
        return $code == 'SUCCESS';
    }

    /**
     * 输出XML信息
     *
     * @return string
     */
    public function __toString()
    {
        $this->setSign();
        return $this->toXml();
    }
}
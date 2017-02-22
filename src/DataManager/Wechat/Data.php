<?php
namespace EasyPay\DataManager\Wechat;

use Ant\Support\Arr;
use DOMDocument;
use EasyPay\Config;
use EasyPay\Exception\PayException;
use EasyPay\Exception\PayFailException;
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

        foreach ($this->items as $key => $value) {
            $item = $dom->createElement($key);
            $item->appendChild($dom->createCDATASection($value));
            $xml->appendChild($item);
        }

        $dom->appendChild($xml);
        return $dom->saveXML();
    }

    /**
     * 释放生成器结果
     */
    public function free()
    {
        $this->items = [];
    }

    /**
     * 设置签名
     */
    public function setSign()
    {
        $this->createNonceStr();
        $this->sign = $this->makeSign();
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
                $result = md5($this->buildData());
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
    protected function buildData()
    {
        $data = $this->items;
        // 删除签名与key
        Arr::forget($data, ['sign','key']);
        // 删除空数据
        $this->removalEmpty($data);
        // 将Key以Ascii表进行排序
        ksort($data);

        if (!$data['key'] = $this->getOption('key')) {
            throw new PayParamException('商户支付密钥不存在,请检查参数');
        }

        // 构造完成后,使用urldecode进行解码
        return urldecode(http_build_query($data));
    }

    /**
     * 检查返回结果是否正确
     */
    public function checkResult()
    {
        //通信是否成功
        if (!$this->isSuccess($this->return_code)) {
            throw new PayException($this, $this->return_msg);
        }

        //交易是否发起
        if (!$this->isSuccess($this->result_code)) {
            //抛出错误码与错误信息
            throw new PayFailException(
                $this,$this->err_code_des,$this->err_code
            );
        }

        //签名是否一致
        if (!$this->offsetExists('sign') || $this->sign != $this->makeSign()) {
            throw new SignVerifyFailException($this, '返回结果错误,签名校验失败');
        }
    }

    protected function getOption($name)
    {
        return Config::wechat($name);
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
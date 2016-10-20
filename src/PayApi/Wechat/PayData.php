<?php
namespace EasyPay\PayApi\Wechat;

use DOMDocument;
use EasyPay\Config;
use EasyPay\PayApi\Collection;
use EasyPay\Exception\PayException;
use EasyPay\Exception\PayFailException;
use EasyPay\Exception\PayParamException;
use EasyPay\Exception\SignVerifyFailException;

/**
 * 支付信息构造器,只负责生成数据流
 *
 * Class PayDataBuilder
 * @package PayApi\Wechat
 */
class PayData extends Collection
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

        foreach($this->items as $key => $value){
            $item = $dom->createElement($key);
            $item->appendChild($dom->createCDATASection($value));

            $xml->appendChild($item);
        }

        $dom->appendChild($xml);
        return $dom->saveXML();
    }

    /**
     * 生成签名(每次都重新生成,确保是最新参数生成的签名)
     *
     * @return string
     * @see https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=4_3
     */
    public function makeSign()
    {
        $url = $this->toUrlParam();
        $string = md5($url);
        $this->sign = strtoupper($string);

        return $this->sign;
    }

    /**
     * 检查参数是否存在
     *
     * @param $key
     * @return bool
     */
    public function isExist($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * 释放生成器结果
     */
    public function free()
    {
        $this->items = [];
    }

    /**
     * 生成URL参数
     *
     * @return string
     */
    protected function toUrlParam()
    {
        ksort($this->items);
        $items = $this->filterItems($this->items);
        if(! $key = Config::wechat('key')){
            throw new PayParamException('商户支付密钥不存在,请检查参数');
        }
        $items['key'] = $key;

        $buff = "";
        foreach ($items as $k => $v)
        {
            $buff .= $k . "=" . $v . "&";
        }

        return trim($buff,'&');
    }

    /**
     * 筛选参数
     *
     * @param $items
     * @return array
     */
    protected function filterItems($items)
    {
        $data = [];
        foreach($items as $key => $value){
            if(!($key === 'sign' || empty($value))){
                $data[$key] = $value;
            }
        }

        return $data;
    }


    /**
     * 产生随机字符串
     *
     * @param int $length
     * @return string
     */
    protected function createNonceStr($length = 32)
    {
        if(!$this->nonce_str){
            $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
            $this->nonce_str = "";
            for ( $i = 0; $i < $length; $i++ )  {
                $this->nonce_str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
            }
        }

        return $this->nonce_str;
    }

    /**
     * 检查返回结果是否正确
     */
    public function checkResult()
    {
        //通信是否成功
        if(!$this->isSuccess($this->return_code)){
            throw new PayException($this,$this->return_msg);
        }

        //交易是否发起
        if(!$this->isSuccess($this->result_code)){
            //抛出错误码与错误信息
            throw new PayFailException(
                $this,$this->err_code_des,$this->err_code
            );
        }

        //签名是否一致
        if(!$this->isExist('sign') || !($this->makeSign() == $this['sign'])){
            throw new SignVerifyFailException($this);
        }

        return $this;
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
        $this->createNonceStr();
        $this->makeSign();
        return $this->toXml();
    }
}
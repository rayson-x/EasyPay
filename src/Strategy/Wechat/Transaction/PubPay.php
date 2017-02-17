<?php
namespace EasyPay\Strategy\Wechat\Transaction;

use EasyPay\Strategy\Wechat\Data;
use EasyPay\Strategy\Wechat\BaseWechatStrategy;

/**
 * 微信公众号支付
 *
 * Class PubPay
 * @package EasyPay\Stratgy\Wechat\Transaction
 */
class PubPay extends BaseWechatStrategy
{
    /**
     * 请求微信公众号支付接口,返回Js api使用的Json数据
     *
     * @return string
     */
    public function execute()
    {
        $this->checkOption();

        $result = $this->sendHttpRequest('POST', static::INIT_ORDER_URL, $this->payData);

        // 生成Js api使用的Json数据
        $data = new Data([
            'appId'     =>  $result['appid'],
            'timeStamp' =>  (string)time(),
            'nonceStr'  =>  substr(md5(uniqid()),0,18).date("YmdHis"),
            'package'   =>  "prepay_id={$result['prepay_id']}",
            'signType'  =>  'MD5',
        ]);

        $data['paySign'] = $data->makeSign();

        return $data->toJson();
    }

    public function checkOption()
    {
        $this->payData->trade_type = 'JSAPI';
        // 检查必要参数是否存在
        $this->payData->checkParamsExits([
            'appid', 'mch_id', 'body', 'out_trade_no','total_fee',
            'spbill_create_ip', 'notify_url','trade_type','product_id',
        ]);

        // Todo 剔除无用参数
    }
}
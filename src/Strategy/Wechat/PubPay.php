<?php
namespace EasyPay\Strategy\Wechat;

use EasyPay\DataManager\Wechat\Data;

/**
 * 请求微信公众号支付接口,返回Js api使用的Json数据
 *
 * Class PubPay
 * @package EasyPay\Stratgy\Wechat\Transaction
 */
class PubPay extends BaseWechatStrategy
{
    /**
     * 公众号支付必填参数
     *
     * @var array
     */
    protected $requireParamsList = [
        'appid', 'mch_id', 'body', 'out_trade_no','total_fee',
        'spbill_create_ip', 'notify_url','trade_type','openid',
    ];

    /**
     * 微信支付所有可填参数列表
     *
     * @var array
     */
    protected $apiParamsList = [
        'appid', 'mch_id', 'body', 'out_trade_no','total_fee',
        'spbill_create_ip', 'notify_url','trade_type','product_id',
        'device_info','sign_type','detail','attach','fee_type',
        'time_start','time_expire','goods_tag','limit_pay','openid'
    ];

    /**
     * @return string
     */
    protected function getRequestMethod()
    {
        return 'POST';
    }

    /**
     * @return string
     */
    protected function getRequestTarget()
    {
        return BaseWechatStrategy::INIT_ORDER_URL;
    }

    /**
     * 生成Http请求Body内容
     *
     * @return \EasyPay\DataManager\Wechat\Data
     */
    protected function buildData()
    {
        // 设定交易模式为公众号支付
        $this->payData->trade_type = 'JSAPI';
        // 检查必要参数是否存在
        $this->payData->checkParamsExits($this->requireParamsList);
        // 选中合法参数,将除下列以外的参数全部剔除
        $this->payData->selectedParams($this->apiParamsList);

        return $this->payData;
    }

    /**
     * @param $result
     * @return mixed
     */
    protected function handleResult($result)
    {
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
}
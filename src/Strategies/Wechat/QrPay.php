<?php

namespace EasyPay\Strategies\Wechat;

/**
 * 微信扫码支付
 *
 * Class QrPay
 * @package EasyPay\Stratgy\Wechat\Transaction
 */
class QrPay extends BaseWechatStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function buildData()
    {
        $payData = parent::buildData();
        // 声明交易方式为扫码支付
        $payData->trade_type = 'NATIVE';

        return $payData;
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return [
            'appid', 'mch_id', 'body', 'out_trade_no','total_fee',
            'spbill_create_ip', 'notify_url','product_id'
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return [
            'appid', 'mch_id', 'body', 'out_trade_no','total_fee',
            'spbill_create_ip', 'notify_url','trade_type','product_id',
            'device_info','sign_type','detail','attach','fee_type',
            'time_start','time_expire','goods_tag','limit_pay','openid',
            'sub_mch_id', 'sub_appid'
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequestMethod()
    {
        return 'POST';
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequestTarget()
    {
        return BaseWechatStrategy::INIT_ORDER_URL;
    }

    /**
     * 生成二维码地址
     *
     * @param $result
     * @return string
     */
    protected function handleData($result)
    {
        return parent::handleData($result)->code_url;
    }
}
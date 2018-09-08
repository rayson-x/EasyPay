<?php

namespace EasyPay\Strategies\Wechat;

use EasyPay\Config;
use EasyPay\Strategies\Wechat\BaseWechatStrategy;

/**
 * 微信企业付款
 *
 * Class Transfers
 * @package EasyPay\Strategies\Wechat
 */
class Transfers extends BaseWechatStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function buildData()
    {
        $this->payData->mchid = $this->payData->mch_id;
        $this->payData->mch_appid = $this->payData->appid;

        parent::buildData();
        // 微信计费单位为分
        $this->payData->amount *= 100;

        return $this->payData;
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return [
            'mch_appid','mchid','partner_trade_no','openid',
            'check_name','amount','desc','spbill_create_ip',
            'ssl_cert_path','ssl_key_path'
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return [
            'mch_appid','mchid','partner_trade_no','openid','check_name','amount',
            'desc','spbill_create_ip','device_info','nonce_str','re_user_name'
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
        return BaseWechatStrategy::TRANSFERS_URL;
    }
}
<?php

namespace EasyPay\Strategies\Wechat;

use EasyPay\Exception\PayParamException;

/**
 * 微信企业付款
 *
 * Class Transfer
 * @package EasyPay\Strategies\Wechat
 */
class Transfer extends BaseWechatStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function buildData()
    {
        $this->payData->mchid = $this->payData->mch_id;
        $this->payData->mch_appid = $this->payData->appid;

        if (!$this->payData->getOption('ssl_key_path') || !$this->payData->getOption('ssl_cert_path')) {
            throw new PayParamException('企业转账时必须配置ssl_key_path与ssl_cert_path');
        }

        if (!is_string($this->payData->check_name)) {
            $this->payData->check_name = $this->payData->check_name
                ? 'FORCE_CHECK'
                : 'NO_CHECK';
        }

        return parent::buildData();
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return [
            'partner_trade_no','openid', 'amount',
            'desc','spbill_create_ip',
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
        return BaseWechatStrategy::TRANSFER_URL;
    }
}

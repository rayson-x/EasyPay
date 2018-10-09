<?php

namespace EasyPay\Strategies\Wechat;

use EasyPay\Exception\PayParamException;

/**
 * 微信企业付款查询
 *
 * Class TransferQuery
 * @package EasyPay\Strategies\Wechat
 */
class TransferQuery extends BaseWechatStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function buildData()
    {
        if (!$this->payData->getOption('ssl_key_path') || !$this->payData->getOption('ssl_cert_path')) {
            throw new PayParamException('查询企业转账时必须配置ssl_key_path与ssl_cert_path');
        }

        return parent::buildData();
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return ['appid','mch_id','partner_trade_no'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return ['appid','mch_id','partner_trade_no','nonce_str'];
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
        return BaseWechatStrategy::TRANSFER_QUERY_URL;
    }
}
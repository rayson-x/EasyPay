<?php
namespace EasyPay\Strategy\Wechat;


class TransfersQuery extends BaseWechatStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return ['appid','mch_id','partner_trade_no','ssl_cert_path','ssl_key_path'];
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
        return BaseWechatStrategy::TRANSFERS_QUERY_URL;
    }
}
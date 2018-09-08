<?php

namespace EasyPay\Strategies\Wechat;

use EasyPay\Exception\PayParamException;

/**
 * 退款
 *
 * Class Refund
 * @package EasyPay\Strategies\Wechat\Transaction
 */
class Refund extends BaseWechatStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function buildData()
    {
        if (!($this->payData['out_trade_no'] || $this->payData['transaction_id'])) {
            throw new PayParamException("缺少订单号,请检查参数");
        }

        // 微信计费单位为分
        $this->payData->refund_fee *= 100;
        $this->payData->total_fee *= 100;

        return parent::buildData();
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return [
            'appid', 'mch_id', 'total_fee', 'refund_fee',
            'out_refund_no', 'ssl_cert_path','ssl_key_path'
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return [
            'appid', 'mch_id', 'out_refund_no', 'total_fee',
            'refund_fee', 'refund_desc', 'device_info', 'sign_type',
            'transaction_id', 'out_trade_no', 'refund_account',
            'refund_fee_type', 'sub_appid', 'sub_mch_id'
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
        return BaseWechatStrategy::REFUND_URL;
    }
}
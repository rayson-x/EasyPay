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
        if (!$this->payData['out_trade_no'] && !$this->payData['transaction_id']) {
            throw new PayParamException('订单退款必须填写[out_trade_no,transaction_id]中任意一个订单号');
        }

        if (!$this->payData->getOption('ssl_key_path') || !$this->payData->getOption('ssl_cert_path')) {
            throw new PayParamException('订单退款必须设置ssl密钥与证书');
        }

        return parent::buildData();
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return [
            'appid', 'mch_id', 'total_fee',
            'refund_fee', 'out_refund_no',
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
            'refund_fee_type', 'sub_appid', 'sub_mch_id',
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

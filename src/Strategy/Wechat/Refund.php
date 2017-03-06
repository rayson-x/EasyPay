<?php
namespace EasyPay\Strategy\Wechat;

use EasyPay\Exception\PayParamException;
use EasyPay\Strategy\Wechat\BaseWechatStrategy;

/**
 * 退款
 *
 * Class Refund
 * @package EasyPay\Strategy\Wechat\Transaction
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

        return parent::buildData();
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return [
            'appid', 'mch_id','total_fee','refund_fee',
            'op_user_id','ssl_cert_path','ssl_key_path'
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return [
            'appid', 'mch_id','out_refund_no','total_fee',
            'refund_fee','op_user_id','device_info','sign_type',
            'transaction_id','out_trade_no','refund_account',
            'refund_fee_type'
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
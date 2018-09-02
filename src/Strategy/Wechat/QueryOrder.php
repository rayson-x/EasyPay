<?php

namespace EasyPay\Strategy\Wechat;

use EasyPay\Exception\PayParamException;
use EasyPay\Strategy\Wechat\BaseWechatStrategy;

/**
 * 查询支付订单
 *
 * Class QueryOrder
 * @package EasyPay\Strategy\Wechat\Transaction
 */
class QueryOrder extends BaseWechatStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function buildData()
    {
        if (!($this->payData['out_trade_no'] || $this->payData['transaction_id'])) {
            throw new PayParamException('缺少订单号,请检查参数');
        }

        return parent::buildData();
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return ['appid', 'mch_id'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return [
            'appid', 'mch_id', 'sub_appid', 'out_trade_no',
            'transaction_id', 'sign_type', 'sub_mch_id'
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
        return BaseWechatStrategy::QUERY_ORDER_URL;
    }
}
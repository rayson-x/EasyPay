<?php

namespace EasyPay\Strategies\Wechat;

use EasyPay\Exception\PayParamException;
use EasyPay\Strategies\Wechat\BaseWechatStrategy;

/**
 * 查询支付订单
 *
 * Class QueryOrder
 * @package EasyPay\Strategies\Wechat\Transaction
 */
class QueryOrder extends BaseWechatStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function buildData()
    {
        if (!$this->payData['out_trade_no'] && !$this->payData['transaction_id']) {
            throw new PayParamException('查询订单必须填写[out_trade_no,transaction_id]中任意一个订单号');
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
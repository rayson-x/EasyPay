<?php

namespace EasyPay\Strategies\Wechat;

/**
 * 关闭支付订单
 *
 * Class CloseOrder
 * @package EasyPay\Strategies\Wechat\Transaction
 * @see https://pay.weixin.qq.com/wiki/doc/api/native_sl.php?chapter=9_3
 */
class CloseOrder extends BaseWechatStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return ['appid', 'mch_id', 'out_trade_no'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return ['appid', 'mch_id', 'sub_appid', 'sub_mch_id', 'out_trade_no', 'sign_type'];
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
        return BaseWechatStrategy::CLOSE_ORDER_URL;
    }
}

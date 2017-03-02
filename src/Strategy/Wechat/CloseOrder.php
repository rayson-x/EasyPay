<?php
namespace EasyPay\Strategy\Wechat;

/**
 * 关闭支付订单
 *
 * Class CloseOrder
 * @package EasyPay\Strategy\Wechat\Transaction
 */
class CloseOrder extends BaseWechatStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return ['appid','mch_id','out_trade_no'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return ['appid','mch_id','out_trade_no','sign_type'];
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
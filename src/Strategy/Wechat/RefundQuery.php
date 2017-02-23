<?php
namespace EasyPay\Strategy\Wechat;

use EasyPay\Exception\PayParamException;
use EasyPay\Strategy\Wechat\BaseWechatStrategy;

/**
 * 查询退款信息
 *
 * Class RefundQuery
 * @package EasyPay\Strategy\Wechat\Transaction
 */
class RefundQuery extends BaseWechatStrategy
{
    /**
     * @return string
     */
    protected function getRequestMethod()
    {
        return 'POST';
    }

    /**
     * @return string
     */
    protected function getRequestTarget()
    {
        return BaseWechatStrategy::REFUND_QUERY_URL;
    }

    /**
     * 生成Http请求Body内容
     *
     * @return \EasyPay\DataManager\Wechat\Data
     */
    protected function buildData()
    {
        if(
            !$this->payData->transaction_id &&
            !$this->payData->out_trade_no &&
            !$this->payData->out_refund_no &&
            !$this->payData->refund_id)
        {
            throw new PayParamException('缺少订单号,订单号可为退款订单号');
        }

        $this->payData->checkParamsExits(['appid','mch_id']);

        return $this->payData;
    }
}
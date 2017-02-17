<?php
namespace EasyPay\Strategy\Wechat\Transaction;

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
    public function execute()
    {
        $this->payData->checkParamsExits(['appid','mch_id']);

        if(
            !$this->payData->transaction_id &&
            !$this->payData->out_trade_no &&
            !$this->payData->out_refund_no &&
            !$this->payData->refund_id)
        {
            throw new PayParamException('缺少订单号,订单号可为退款订单号');
        }

        return $this->sendHttpRequest('POST', static::REFUND_QUERY_URL, $this->payData);
    }
}
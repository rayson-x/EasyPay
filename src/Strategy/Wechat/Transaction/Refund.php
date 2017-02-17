<?php
namespace EasyPay\Strategy\Wechat\Transaction;

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
    public function execute()
    {
        $this->payData->checkParamsExits(
            ['appid', 'mch_id','out_refund_no','total_fee','refund_fee','op_user_id']
        );

        if (!($this->payData->out_trade_no || $this->payData->transaction_id)) {
            throw new PayParamException("缺少订单号,请检查参数");
        }

        // Todo 退款后,签名验证失败
        return $this->sendHttpRequest('POST', static::REFUND_URL, $this->payData);
    }
}
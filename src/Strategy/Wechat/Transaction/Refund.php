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
            ['appid', 'mch_id','out_refund_no','total_fee','refund_fee']
        );

        if (!($this->payData->out_trade_no || $this->payData->transaction_id)) {
            throw new PayParamException("缺少订单号,请检查参数");
        }

        // Todo 退款时返回数据检测修复
        return $this->sendHttpRequest('POST', static::REFUND_URL, $this->payData);
    }
}
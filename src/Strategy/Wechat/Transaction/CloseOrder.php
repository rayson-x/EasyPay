<?php
namespace EasyPay\Strategy\Wechat\Transaction;

use EasyPay\Strategy\Wechat\BaseWechatStrategy;

/**
 * 关闭支付订单
 *
 * Class CloseOrder
 * @package EasyPay\Strategy\Wechat\Transaction
 */
class CloseOrder extends BaseWechatStrategy
{
    public function execute()
    {
        $this->payData->checkParamsExits(['appid','mch_id','out_trade_no']);

        $this->sendHttpRequest('POST', static::CLOSE_ORDER_URL, $this->payData);
    }
}
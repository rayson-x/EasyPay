<?php
namespace EasyPay\Strategy\Wechat\Transaction;

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
     * 根据指定订单号查询订单详细,返回订单数据集
     *
     * @return \EasyPay\Strategy\Wechat\Data
     */
    public function execute()
    {
        $this->payData->checkParamsExits(['appid','mch_id']);

        if (!($this->payData->out_trade_no || $this->payData->transaction_id)) {
            throw new PayParamException('缺少订单号,请检查参数');
        }

        return $this->sendHttpRequest('POST', static::QUERY_ORDER_URL, $this->payData);
    }
}
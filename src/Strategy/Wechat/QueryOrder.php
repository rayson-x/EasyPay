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
        return BaseWechatStrategy::QUERY_ORDER_URL;
    }

    /**
     * 生成Http请求Body内容
     *
     * @return \EasyPay\DataManager\Wechat\Data
     */
    protected function buildData()
    {
        // 检查必要参数是否存在
        $this->payData->checkParamsExits(['appid','mch_id']);

        if (!($this->payData->out_trade_no || $this->payData->transaction_id)) {
            throw new PayParamException('缺少订单号,请检查参数');
        }

        // 选中合法参数,将除下列以外的参数全部剔除
        $this->payData->selectedParams(['appid','mch_id','out_trade_no','sign_type']);

        return $this->payData;
    }
}
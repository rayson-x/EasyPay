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
        return BaseWechatStrategy::CLOSE_ORDER_URL;
    }

    /**
     * 生成Http请求Body内容
     *
     * @return \EasyPay\DataManager\Wechat\Data
     */
    protected function buildData()
    {
        // 检查必要参数是否存在
        $this->payData->checkParamsExits(['appid','mch_id','out_trade_no']);
        // 将多余参数剔除
        $this->payData->selectedParams(['appid','mch_id','out_trade_no','sign_type']);

        return $this->payData;
    }

    /**
     * @param $result
     * @return mixed
     */
    protected function handleResult($result)
    {
        return $result;
    }
}
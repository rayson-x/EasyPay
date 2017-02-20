<?php
namespace EasyPay\Strategy\Wechat;

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
    /**
     * 退款必填参数
     *
     * @var array
     */
    protected $requireParamsList = [
        'appid', 'mch_id','total_fee','refund_fee','op_user_id','ssl_cert_path','ssl_key_path'
    ];

    /**
     * 退款所有可填参数列表
     *
     * @var array
     */
    protected $apiParamsList = [
        'appid', 'mch_id','out_refund_no','total_fee',
        'refund_fee','op_user_id','device_info','sign_type',
        'transaction_id','out_trade_no','refund_account',
        'refund_fee_type'
    ];

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
        return BaseWechatStrategy::REFUND_URL;
    }

    /**
     * 生成Http请求Body内容
     *
     * @return \EasyPay\DataManager\Wechat\Data
     */
    protected function buildData()
    {
        if (!($this->payData->out_trade_no || $this->payData->transaction_id)) {
            throw new PayParamException("缺少订单号,请检查参数");
        }

        // 检查必要参数是否存在
        $this->payData->checkParamsExits($this->requireParamsList);
        // 选中合法参数,将除下列以外的参数全部剔除
        $this->payData->selectedParams($this->apiParamsList);

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
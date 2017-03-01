<?php
namespace EasyPay\Strategy\Ali;

use Ant\Support\Arr;
use EasyPay\Exception\PayParamException;

class Refund extends BaseAliStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function getMethod()
    {
        return BaseAliStrategy::REFUND;
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequireParamsList()
    {
        if (!$this->payData['out_trade_no'] && !$this->payData['trade_no']) {
            throw new PayParamException("缺少订单号");
        }

        return ['app_id','refund_amount'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getApiParamsList()
    {
        return [
            'app_id', 'method', 'format', 'charset', 'sign_type', 'sign',
            'timestamp', 'version', 'app_auth_token', 'biz_content'
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBinContent()
    {
        $data = [
            // 商户唯一订单号
            'out_trade_no'          =>  $this->payData['out_trade_no'],
            // 支付宝唯一订单号
            'trade_no'              =>  $this->payData['trade_no'],
            // 退款金额
            'refund_amount'         =>  $this->payData['refund_amount'],
            // 退款说明
            'refund_reason'         =>  $this->payData['refund_reason'],
            // 标识一次退款请求,如果退款全额并且此参数为空时,默认以商户订单号作为退款单号
            'out_request_no'        =>  $this->payData['trade_no'],
            // 商户的操作员编号
            'operator_id'           =>  $this->payData['trade_no'],
            // 商户的门店编号
            'store_id'              =>  $this->payData['trade_no'],
            // 商户的终端编号
            'terminal_id'           =>  $this->payData['trade_no'],
        ];

        Arr::removalEmpty($data);

        return $data;
    }
}
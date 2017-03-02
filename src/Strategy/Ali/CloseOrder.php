<?php
namespace EasyPay\Strategy\Ali;

use Ant\Support\Arr;
use EasyPay\Exception\PayParamException;

/**
 * 支付宝关闭订单功能
 *
 * Class CloseOrder
 * @package EasyPay\Strategy\Ali
 */
class CloseOrder extends BaseAliStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function buildData()
    {
        if (!$this->payData['out_trade_no'] && !$this->payData['trade_no']) {
            throw new PayParamException("缺少订单号");
        }

        return parent::buildData();
    }

    /**
     * {@inheritDoc}
     */
    protected function getMethod()
    {
        return BaseAliStrategy::CLOSE_ORDER;
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return ['app_id'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return [
            'app_id', 'method', 'format', 'charset', 'sign_type', 'sign',
            'timestamp', 'version', 'notify_url', 'app_auth_token', 'biz_content'
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
            // 商户的操作员编号
            'operator_id'           =>  $this->payData['trade_no'],
        ];

        Arr::removalEmpty($data);

        return $data;
    }
}
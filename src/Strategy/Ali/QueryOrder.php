<?php
namespace EasyPay\Strategy\Ali;

use EasyPay\Exception\PayParamException;

/**
 * 支付宝订单查询
 *
 * Class QueryOrder
 * @package EasyPay\Strategy\Ali
 */
class QueryOrder extends BaseAliStrategy
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
        return BaseAliStrategy::QUERY_ORDER;
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
            // 支付宝订单号
            'trade_no'              =>  $this->payData['trade_no'],
        ];

        array_removal_empty($data);

        return $data;
    }
}
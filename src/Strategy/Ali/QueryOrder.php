<?php
namespace EasyPay\Strategy\Ali;

use Ant\Support\Arr;
use EasyPay\Exception\PayParamException;

class QueryOrder extends BaseAliStrategy
{
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
    protected function getRequireParamsList()
    {
        if (!$this->payData['out_trade_no'] && !$this->payData['trade_no']) {
            throw new PayParamException("缺少订单号");
        }

        return ['app_id'];
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
            // 唯一订单号
            'out_trade_no'          =>  $this->payData['out_trade_no'],
            'trade_no'              =>  $this->payData['trade_no'],
        ];

        Arr::removalEmpty($data);

        return $data;
    }
}
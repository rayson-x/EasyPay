<?php

namespace EasyPay\Strategies\Wechat;

use EasyPay\TradeData\Wechat\TradeData;

/**
 * todo 待实际账号测试
 * 微信app支付
 *
 * Class AppPay
 * @package EasyPay\Strategies\Wechat
 * @see https://pay.weixin.qq.com/wiki/doc/api/app/app_sl.php?chapter=9_1
 */
class AppPay extends BaseWechatStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return [
            'appid', 'mch_id', 'body', 'out_trade_no',
            'total_fee', 'spbill_create_ip', 'notify_url'
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return [
            'appid', 'mch_id', 'sub_appid', 'sub_mch_id', 'device_info',
            'body', 'detail', 'attach', 'out_trade_no', 'total_fee',
            'spbill_create_ip', 'time_start', 'time_expire', 'notify_url',
            'goods_tag', 'limit_pay'
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequestMethod()
    {
        return 'POST';
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequestTarget()
    {
        return BaseWechatStrategy::INIT_ORDER_URL;
    }

    /**
     * {@inheritDoc}
     */
    protected function buildData()
    {
        $payData = parent::buildData();
        // 设定交易模式为app支付
        $payData->trade_type = 'APP';

        return $payData;
    }

    /**
     * 生成app预支付标识
     *
     * @param $result
     * @return TradeData
     */
    protected function handleData($result)
    {
        $result = parent::handleData($result);

        // 生成app预支付标识
        $data = new TradeData([
            'appid'     => $result->appId,
            'partnerid' => $result->mchId,
            'prepayid'  => $result->prepay_id,
            'package'   => 'Sign=WXPay',
            'timestamp' => time(),
        ], $this->payData->getOptions());

        $data->setSign();

        return $data;
    }
}
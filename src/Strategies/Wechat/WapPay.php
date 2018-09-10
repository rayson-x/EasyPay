<?php

namespace EasyPay\Strategies\Wechat;

/**
 * todo 待申请h5支付后测试
 * 微信h5支付,在mobile wab中调起微信支付
 *
 * Class WapPay
 * @package EasyPay\Strategies\Wechat
 * @see https://pay.weixin.qq.com/wiki/doc/api/H5_sl.php?chapter=9_20&index=1
 */
class WapPay extends BaseWechatStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return [
            'appid', 'mch_id', 'body', 'out_trade_no', 'total_fee',
            'spbill_create_ip', 'notify_url', 'wap_url', 'wap_name'
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return [
            'appid', 'mch_id', 'device_info', 'sign_type', 'body', 'detail',
            'attach', 'out_trade_no', 'fee_type', 'total_fee', 'spbill_create_ip',
            'time_start', 'time_expire', 'notify_url', 'product_id', 'limit_pay',
            'openid', 'trade_type', 'scene_info'
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
        // 检查必要参数是否存在
        $this->payData->checkParamsEmpty($this->getRequireParams());
        // 构造scene_info
        $this->payData->scene_info = json_encode([
            'h5_info' => [
                'type'      =>  'Wap',
                'wap_url'   =>  $this->payData->wap_url,
                'wap_name'  =>  $this->payData->wap_name
            ]
        ]);
        // 填入所有可用参数,并将不可用参数清除
        $this->payData->selected($this->getFillParams());
        // 设定交易模式为手机h5支付
        $this->payData->trade_type = 'MWEB';

        return $this->payData;
    }

    /**
     * 生成调起微信app的url
     *
     * @param $result
     * @return string
     */
    protected function handleData($result)
    {
        $result = parent::handleData($result);

        $wabUrl = $result['mweb_url'];
        if ($returnUrl = $this->payData->getOriginal('return_url')) {
            $wabUrl .= '&redirect_url=' . urlencode($returnUrl);
        }

        return $wabUrl;
    }
}
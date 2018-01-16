<?php
namespace EasyPay\Strategy\Wechat;

/**
 * todo 待申请h5支付后测试
 * 微信h5支付,在mobile wab中调起微信支付
 *
 * Class WapPay
 * @package EasyPay\Strategy\Wechat
 * @see https://pay.weixin.qq.com/wiki/doc/api/H5_sl.php?chapter=15_4
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
        // 设定交易模式为公众号支付
        $this->payData->trade_type = 'MWEB';
        // 微信计费单位为分
        $this->payData->total_fee *= 100;
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

        return $this->payData;
    }

    /**
     * 生成公众号支付使用的json数据
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
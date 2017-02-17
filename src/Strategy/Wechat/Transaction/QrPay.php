<?php
namespace EasyPay\Strategy\Wechat\Transaction;

use EasyPay\Strategy\Wechat\BaseWechatStrategy;

/**
 * 微信扫码支付
 *
 * Class QrPay
 * @package EasyPay\Stratgy\Wechat\Transaction
 */
class QrPay extends BaseWechatStrategy
{
    /**
     * 请求微信扫码支付接口,获取二维码url
     *
     * @return string
     */
    public function execute()
    {
        $this->checkOption();

        $result = $this->sendHttpRequest('POST', static::INIT_ORDER_URL, $this->payData);

        return $result['code_url'];
    }

    public function checkOption()
    {
        $this->payData->trade_type = 'NATIVE';
        // 检查必要参数是否存在
        $this->payData->checkParamsExits([
            'appid', 'mch_id', 'body', 'out_trade_no','total_fee',
            'spbill_create_ip', 'notify_url','trade_type','product_id',
        ]);

        // Todo 剔除无用参数
    }
}
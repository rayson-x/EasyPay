<?php
namespace EasyPay\Strategy\Wechat;

use EasyPay\Config;
use EasyPay\Strategy\Wechat\BaseWechatStrategy;

/**
 * 微信企业付款
 *
 * Class Transfers
 * @package EasyPay\Strategy\Wechat
 */
class Transfers extends BaseWechatStrategy
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
        return BaseWechatStrategy::TRANSFERS_URL;
    }

    /**
     * 生成Http请求Body内容
     *
     * @return \EasyPay\DataManager\Wechat\Data
     */
    protected function buildData()
    {
        $this->payData['mch_appid'] = Config::wechat('appid');
        $this->payData['mchid'] = Config::wechat('mch_id');

        $this->payData->checkParamsExits(
            ['mch_appid','mchid','partner_trade_no','openid','check_name','amount','desc','spbill_create_ip']
        );

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
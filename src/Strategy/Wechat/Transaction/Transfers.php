<?php
namespace EasyPay\Strategy\Wechat\Transaction;

use EasyPay\Config;
use EasyPay\Strategy\Wechat\BaseWechatStrategy;

class Transfers extends BaseWechatStrategy
{
    public function execute()
    {
        $this->payData['mch_appid'] = Config::wechat('appid');
        $this->payData['mchid'] = Config::wechat('mch_id');

        $this->payData->checkParamsExits(
            ['mch_appid','mchid','partner_trade_no','openid','check_name','amount','desc','spbill_create_ip']
        );

        return $this->sendHttpRequest('POST', static::TRANSFERS_URL, $this->payData);
    }
}
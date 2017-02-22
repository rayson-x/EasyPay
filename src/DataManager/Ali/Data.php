<?php
namespace EasyPay\DataManager\Ali;

use EasyPay\Config;
use EasyPay\Utils\Rsa;
use EasyPay\Exception\PayException;
use EasyPay\DataManager\BaseDataManager;

class Data extends BaseDataManager
{
    protected function getOption($name)
    {
        return Config::ali($name);
    }

    public function makeSign()
    {
        $signType = $this->offsetExists('sign_type') ? $this->sign_type : 'RSA';

        switch ($signType) {
            case "RSA" :
                break;
            case "RSA2" :
                break;
            default :
                throw new PayException("签名类型错误");
        }
    }

    protected function buildData()
    {
        // 构造交易数据
    }

    protected function buildBizData()
    {
        // 构造订单数据

    }
}
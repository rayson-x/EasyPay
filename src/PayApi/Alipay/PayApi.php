<?php
namespace EasyPay\PayApi\Alipay;

use EasyPay\Config;
use EasyPay\Interfaces\PayApiInterface;
use EasyPay\Exception\PayParamException;

class PayApi implements PayApiInterface
{
    // 发起订单URL
    const ORDER_URL = 'https://openapi.alipay.com/gateway.do';
    // 查询订单URL
    const ORDER_QUERY_URL = 'https://openapi.alipay.com/gateway.do';
    // 关闭订单URL
    const CLOSE_ORDER_URL = 'https://openapi.alipay.com/gateway.do';
    // 退款URL
    const REFUND_URL = 'https://openapi.alipay.com/gateway.do';
    // 查询退款URL
    const REFUND_QUERY_URL = 'https://openapi.alipay.com/gateway.do';
    // 下载对账单地址
    const DOWN_LOAD_BILL_URL = 'https://openapi.alipay.com/gateway.do';

    /**
     * PayApi Construct
     *
     * @param array $option
     */
    public function __construct($option)
    {
        if(!$option instanceof PayData){
            $option = new PayData($option);
        }

        $this->payData = $option;
    }

    public function initOrder()
    {

    }

    public function orderQuery()
    {

    }

    public function closeOrder()
    {

    }

    public function refund()
    {

    }

    public function refundQuery()
    {

    }

    public function downloadBill()
    {

    }

    /**
     * 检查参数是否存在
     *
     * @param array $params
     */
    protected function checkOption(array $params)
    {
        foreach($params as $param){
            if(!$this->payData->$param){
                // 尝试从配置信息中获取参数
                if(!Config::alipay($param)){
                    throw new PayParamException("[$param]不存在,请检查参数");
                }

                $this->payData->$param = Config::alipay($param);
            }
        }
    }

}
<?php
namespace EasyPay\Strategies\Ali;

/**
 * 支付宝网页支付,返回结果为支付宝支付url
 *
 * Class WapPay
 * @package EasyPay\Strategies\Ali
 */
class WapPay extends BaseAliStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function getMethod()
    {
        return BaseAliStrategy::WAP_PAY;
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return ['app_id','subject','out_trade_no','total_amount','product_code'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return [
            'app_id','method','format','return_url','charset','sign_type',
            'sign','timestamp','version','notify_url','biz_content'
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function buildData()
    {
        // 支付单位为分,支付宝支付金额单位为元
        $this->payData->total_amount /= 100;

        return parent::buildData();
    }

    /**
     * {@inheritDoc}
     */
    protected function handleData($data)
    {
        return $this->getServerUrl() . "?" . http_build_query($data);
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBinContent()
    {
        $data = [
            // 交易的具体描述信息
            'body'                  =>  $this->payData['body'],
            // 商品的标题
            'subject'               =>  $this->payData['subject'],
            // 唯一订单号
            'out_trade_no'          =>  $this->payData['out_trade_no'],
            // 订单过期时间
            'timeout_express'       =>  $this->payData['timeout_express'],
            // 交易金额,单位为元,精确到分
            'total_amount'          =>  $this->payData['total_amount'],
            // 收款支付宝用户ID
            'seller_id'             =>  $this->payData['seller_id'],
            // 用户授权码
            'auth_token'            =>  $this->payData['auth_token'],
            // 销售产品码
            'product_code'          =>  $this->payData['product_code'],
            // 商品类型 0—虚拟类商品，1—实物类商品
            'goods_type'            =>  $this->payData['goods_type'],
            // 公用回传参数
            'passback_params'       =>  $this->payData['passback_params'],
            // 优惠参数
            'promo_params'          =>  $this->payData['promo_params'],
            // 业务扩展参数(详细请查看接口文档)
            'extend_params'         =>  $this->payData['extend_params'],
            // 指定用户支付渠道,通过","进行分隔
            'enable_pay_channels'   =>  $this->payData['enable_pay_channels'],
            // 指定用户不可用的渠道,通过","进行分隔
            'disable_pay_channels'  =>  $this->payData['disable_pay_channels'],
            // 商户门店编号
            'store_id'              =>  $this->payData['store_id'],
        ];

        // 清除空参数
        array_removal_empty($data);

        return $data;
    }
}
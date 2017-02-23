<?php
namespace EasyPay\Strategy\Ali;


use Ant\Support\Arr;
use EasyPay\Config;

class WapPay extends BaseAliStrategy
{
    /**
     * 生成Api参数
     *
     * @return array
     */
    protected function buildData()
    {
        // 检查必填参数是否存在
        $this->payData->checkParamsExits(['app_id','subject','out_trade_no','total_amount','product_code']);
        // 设置请求的方法
        $this->payData['method'] = BaseAliStrategy::WAP_PAY;
        // 生成请求参数
        $this->payData['biz_content'] = $this->buildBinContent();
        // 将非法参数剔除
        $this->payData->selectedParams([
            'app_id','method','format','return_url','charset','sign_type',
            'sign','timestamp','version','notify_url','biz_content'
        ]);
        // 生成签名
        $this->payData['sign'] = $this->payData->makeSign();

        return $this->payData->toArray();
    }

    /**
     * 处理数据,支付宝Wap支付为跳转页面,所以此处返回生成好的支付链接
     *
     * @param $data
     * @return string
     */
    protected function handleData($data)
    {
        // 支持沙箱测试
        $url = Config::ali('is_sand_box')
            ? "https://openapi.alipaydev.com/gateway.do?"
            : "https://openapi.alipay.com/gateway.do";

        return $url.http_build_query($data);
    }

    /**
     * 生成请求参数
     *
     * @return array
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
        Arr::removalEmpty($data);

        return $data;
    }
}
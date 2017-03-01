<?php
namespace EasyPay\Strategy\Ali;


use Ant\Support\Arr;

class Transfers extends BaseAliStrategy
{
    /**
     * {@inheritDoc}
     */
    protected function getMethod()
    {
        return BaseAliStrategy::TRANSFERS;
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequireParamsList()
    {
        return ['app_id','out_biz_no','payee_type','payee_account','amount'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getApiParamsList()
    {
        return [
            'app_id','method','format','charset','sign_type','sign',
            'timestamp','version','notify_url','app_auth_token','biz_content'
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function buildBinContent()
    {
        $data = [
            // 商户转账唯一订单号
            'out_biz_no'            =>  $this->payData['out_biz_no'],
            // 收款方账户类型 ALIPAY_USERID 为用户在支付宝的唯一ID,ALIPAY_LOGONID 为支付宝用户登录账户
            'payee_type'            =>  $this->payData['payee_type'],
            // 收款方账户。与payee_type配合使用
            'payee_account'         =>  $this->payData['payee_account'],
            // 转账金额,单位为元,精确到分
            'amount'                =>  $this->payData['amount'],
            // 付款方真实姓名
            'payer_real_name'       =>  $this->payData['payer_real_name'],
            // 付款方显示姓名
            'payer_show_name'       =>  $this->payData['payer_show_name'],
            // 收款方真实姓名
            'payee_real_name'       =>  $this->payData['payee_real_name'],
            // 转账备注（支持200个英文/100个汉字）
            'remark'                =>  $this->payData['remark'],
            // 扩展参数(详细参数参考支付宝文档)
            'ext_param'             =>  $this->payData['ext_param'],
        ];

        Arr::removalEmpty($data);

        return $data;
    }
}
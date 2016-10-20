<?php
namespace EasyPay\PayApi\Wechat;

use EasyPay\Config;
use EasyPay\Exception\PayParamException;
use EasyPay\Interfaces\PayApiInterface;

/**
 * 微信支付接口
 *
 * Class PayApi
 * @package EasyPay\PayApi\Wechat
 */
class PayApi implements PayApiInterface
{
    // 发起订单URL
    const ORDER_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    // 查询订单URL
    const ORDER_QUERY_URL = 'https://api.mch.weixin.qq.com/pay/orderquery';
    // 关闭订单URL
    const CLOSE_ORDER_URL = 'https://api.mch.weixin.qq.com/pay/closeorder';
    // 退款URL
    const REFUND_URL = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
    // 查询退款URL
    const REFUND_QUERY_URL = 'https://api.mch.weixin.qq.com/pay/refundquery';
    // 下载对账单地址
    const DOWN_LOAD_BILL_URL = 'https://api.mch.weixin.qq.com/pay/downloadbill';

    /**
     * @var PayData
     */
    protected $payData;

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

    /**
     * 创建一个订单
     *
     * @return array|object
     */
    public function initOrder()
    {
        $this->checkInitOrderOption();

        /* 构造发送数据 */
        $body = (string)$this->payData;

        /* 发送数据并返回响应数据 */
        return $this->request('POST',static::ORDER_URL,$body);
    }

    /**
     * 查询订单
     *
     * @return array|object
     */
    public function orderQuery()
    {
        $this->checkQueryOrderOption();

        $body = (string)$this->payData;

        return $this->request('POST',static::ORDER_QUERY_URL,$body);
    }

    /**
     * 关闭订单
     *
     * @return array|object
     */
    public function closeOrder()
    {
        $this->checkCloseOrderOption();

        $body = (string)$this->payData;

        return $this->request('POST',static::CLOSE_ORDER_URL,$body);
    }

    /**
     * 发起退款
     *
     * @return array|object
     */
    public function refund()
    {
        $this->checkRefundOption();

        //操作员ID为空时,默认为商户ID
        if(!$this->payData->op_user_id){
            $this->payData->op_user_id = $this->payData->mch_id;
        }

        $body = (string)$this->payData;

        return $this->request('POST',static::REFUND_URL,$body);
    }

    /**
     * 查询退款信息
     *
     * @return array|object
     */
    public function refundQuery()
    {
        $this->checkRefundQueryOption();

        $body = (string)$this->payData;

        return $this->request('POST',static::REFUND_QUERY_URL,$body);
    }

    /**
     * 下载对账单
     *
     * @return array|object
     */
    public function downloadBill()
    {
        $this->checkDownloadBillOption();

        $body = (string)$this->payData;

        return $this->request('POST',static::DOWN_LOAD_BILL_URL,$body);
    }

    /**
     * @param $body
     * @param $url
     * @return array|object
     */
    protected function request($method,$url,$body)
    {
        //TODO;:实现HTTP请求
        $context = stream_context_create([
            'http'=>[
                'method' => $method,
                'header' => "Content-Type: text/xml\r\n",
                'content' => $body,
            ]
        ]);

        $res = file_get_contents($url,false,$context);

        return PayData::createDataFromXML($res)->checkResult();
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
                if(!Config::wechat($param)){
                    throw new PayParamException("[$param]不存在,请检查参数");
                }

                $this->payData->$param = Config::wechat($param);
            }
        }
    }

    /**
     * 检查发起订单必要参数
     * 公众账号ID	   appid
     * 商户号	       mch_id
     * 商品描述	       body
     * 商户订单号	   out_trade_no
     * 总金额	       total_fee
     * 终端IP	       spbill_create_ip
     * 回调地址	       notify_url
     * 交易类型	       trade_type(JSAPI，NATIVE，APP)
     */
    protected function checkInitOrderOption()
    {
        $this->checkOption(
            [ 'appid', 'mch_id', 'body', 'out_trade_no','total_fee', 'spbill_create_ip', 'notify_url','trade_type']
        );
        if($this->isJsApi() && !$this->payData->openid){
            throw new PayParamException('如果"trade_type"是"JSAPI","openid"为必需参数');
        }
        if($this->isNative() && !$this->payData->product_id){
            throw new PayParamException('如果"trade_type"是"NATIVE","product_id"为必需参数');
        }
    }

    /**
     * 查询订单必备参数
     * 公众账号ID	   appid
     * 商户号	       mch_id
     * 订单号	       out_trade_no || transaction_id
     */
    protected function checkQueryOrderOption()
    {
        $this->checkOption(['appid','mch_id']);

        if(!($this->payData->out_trade_no || $this->payData->transaction_id)){
            throw new PayParamException('缺少订单号,请检查参数');
        }
    }

    /**
     * 关闭订单
     * 公众账号ID	   appid
     * 商户号	       mch_id
     * 商户订单号	   out_trade_no
     */
    protected function checkCloseOrderOption()
    {
        $this->checkOption(['appid','mch_id','out_trade_no']);
    }

    /**
     * 发起退款必要参数
     * 公众账号ID	   appid
     * 商户号	       mch_id
     * 退款订单号      out_refund_no
     * 订单金额	       total_fee
     * 退款金额	       refund_fee
     * 订单号	       out_trade_no || transaction_id
     */
    protected function checkRefundOption()
    {
        $this->checkOption(
            [ 'appid', 'mch_id','out_refund_no','total_fee','refund_fee']
        );

        if(!($this->payData->out_trade_no || $this->payData->transaction_id)){
            throw new PayParamException("缺少订单号,请检查参数");
        }
    }

    /**
     * 查询退款必要参数
     * 公众账号ID	   appid
     * 商户号	       mch_id
     * 订单号	       out_trade_no || transaction_id
     * 退款订单号	   out_refund_no || refund_id
     */
    protected function checkRefundQueryOption()
    {
        if(!$this->payData->transaction_id &&
            !$this->payData->out_trade_no &&
            !$this->payData->out_refund_no &&
            !$this->payData->refund_id)
        {
            throw new PayParamException('缺少订单号,订单号可为退款订单号');
        }
    }

    /**
     * 下载对账单
     * 公众账号ID	   appid
     * 商户号	       mch_id
     * 对账单日期	   bill_date
     * 账单类型	       bill_type
     */
    protected function checkDownloadBillOption()
    {
        $this->checkOption(['appid','mch_id','bill_date','bill_type']);
    }

    /**
     * 检查支付模式是否是JSAPI模式
     *
     * @return bool
     */
    protected function isJsApi()
    {
        return $this->payData->trade_type == "JSAPI";
    }

    /**
     * 检查支付模式是否是扫码支付
     *
     * @return bool
     */
    protected function isNative()
    {
        return $this->payData->trade_type == "NATIVE";
    }
}
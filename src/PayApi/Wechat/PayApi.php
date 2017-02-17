<?php
namespace EasyPay\PayApi\Wechat;

use Ant\Http\Request;
use Ant\Http\Response;
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
    // 微信转账地址
    const TRANSFERS_URL = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";

    /**
     * @var PayData
     */
    protected $payData;

    /**
     * PayApi Construct
     *
     * @param array $option
     */
    public function __construct(array $option)
    {
        if (!$option instanceof PayData) {
            $option = new PayData($option);
        }

        $this->payData = $option;
    }

    /**
     * @return PayData
     */
    public function getOptions()
    {
        return $this->payData;
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
        return $this->request('POST', static::ORDER_URL, $body);
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

        return $this->request('POST', static::ORDER_QUERY_URL, $body);
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

        return $this->request('POST', static::CLOSE_ORDER_URL, $body);
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
        if (!$this->payData->op_user_id) {
            $this->payData->op_user_id = $this->payData->mch_id;
        }

        $body = (string)$this->payData;

        return $this->request('POST', static::REFUND_URL, $body);
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

        return $this->request('POST', static::REFUND_QUERY_URL, $body);
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

        return $this->request('POST', static::DOWN_LOAD_BILL_URL, $body);
    }

    /**
     * 微信企业转账
     *
     * @return array|object.
     */
    public function transfers()
    {
        $this->checkTransfersOption();

        return $this->request('POST', static::TRANSFERS_URL, (string)$this->payData);
    }

    /**
     * @param $body
     * @param $url
     * @return array|object
     */
    protected function request($method, $url, $body)
    {
        $request = new Request($method, $url);
        $request->keepImmutability(false);
        $request->withHeader('Content-Type','text/xml')->getBody()->write($body);

        $ch = $this->curlInit($request);
        if (false === $result = curl_exec($ch)) {
            throw new \RuntimeException(curl_error($ch),curl_errno($ch));
        }

        $response = PayData::createDataFromXML(
            (string)Response::createFromResponseStr($result)->getBody()
        );

        $response->checkResult();

        return $response;
    }

    /**
     * @return resource
     */
    protected function curlInit(Request $request)
    {
        // 发起一个请求
        $ch = curl_init((string)$request->getUri());
        // 获取完整的Http流
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if($request->getUri()->getScheme() === 'https') {
            // 关闭严格校验
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
            // 添加SSL证书
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, Config::wechat('sslcert_path'));
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, Config::wechat('sslkey_path'));
        }

        // 设置Http动词
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->getOriginalMethod());
        $header = [];
        foreach($request->getHeaders() as $headerName => $headerValue) {
            if (is_array($headerValue)) {
                $headerValue = implode(',', $headerValue);
            }

            $headerName = implode('-',array_map('ucfirst',explode('-',$headerName)));
            $header[] = sprintf('%s: %s',$headerName,$headerValue);
        }

        // 设置Http header内容
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        // 设置Http body内容
        curl_setopt($ch, CURLOPT_POSTFIELDS, (string)$request->getBody());

        return $ch;
    }

    /**
     * 检查参数是否存在
     *
     * @param array $params
     */
    protected function checkOption(array $params)
    {
        foreach ($params as $param) {
            if (!$this->payData->$param) {
                // 尝试从配置信息中获取参数
                if (!Config::wechat($param)) {
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

        if ($this->isJsApi() && !$this->payData->openid) {
            throw new PayParamException('如果"trade_type"是"JSAPI","openid"为必需参数');
        }

        if ($this->isNative() && !$this->payData->product_id) {
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

        if (!($this->payData->out_trade_no || $this->payData->transaction_id)) {
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

        if (!($this->payData->out_trade_no || $this->payData->transaction_id)) {
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
        if(
            !$this->payData->transaction_id &&
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
     * 微信转账到个人用户
     * 公众号ID        mch_appid
     * 商户号          mch_id
     * 商户订单号      partner_trade_no
     * 用户ID          openid
     * 是否校验实名    check_name
     * 金额/分         amount
     * 付款信息详细    desc
     * IP地址          spbill_create_ip
     */
    protected function checkTransfersOption()
    {
        if (!isset($this->payData->mch_appid)) {
            if (isset($this->payData->appid)) {
                $this->payData->mch_appid = $this->payData->appid;
                unset($this->payData->appid);
            } else {
                $this->payData->mch_appid = Config::wechat('appid');
            }
        }

        if (!isset($this->payData->mchid)) {
            if (isset($this->payData->mch_id)) {
                $this->payData->mchid = $this->payData->mch_id;
                unset($this->payData->appid);
            } else {
                $this->payData->mchid = Config::wechat('mch_id');
            }
        }

        $this->checkOption(['mch_appid','mchid','partner_trade_no','openid','check_name','amount','desc','spbill_create_ip']);
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
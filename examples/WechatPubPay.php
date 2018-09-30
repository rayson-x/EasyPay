<?php
include "bootstrap.php";

use EasyPay\Payment;
use EasyPay\PayFactory;

try {
    // 使用微信公众号支付
    $trade = PayFactory::create(Payment::WX_PUB_PAY);

    // 生成公众号支付,JS-SDK所用的Json数据
    $json = $trade->execute([
        // 公众号支付所需openid
        'openid'            =>  'okUzQw52RfmBwO4H1d8M-bHPo8Vw',
        // 支付信息
        'attach'            => 'wechat pay test',
        // 支付订单信息
        'body'              =>  '微信扫码支付,测试订单',
        // 支付订单号
        'out_trade_no'      => substr(md5(uniqid()), 0, 18) . date("YmdHis"),
        // 支付金额,单位为分
        'total_fee'         =>  '1',
        // 客户端IP
        'spbill_create_ip'  => $_SERVER['SERVER_ADDR'],
    ]);
} catch (\Exception $e) {
    // 打印错误县信息
    echo "错误信息为 : {$e->getMessage()}","<br>";
    echo "错误文件为 : {$e->getFile()}, 错误所在行 : {$e->getLine()}";
    die;
}
?>
<script>
    function onBridgeReady()
    {
        WeixinJSBridge.invoke(
            'getBrandWCPayRequest',
            <?php echo $json?>,
            function(res) { alert(res.err_msg); }
        );
    }

    if (typeof WeixinJSBridge == "undefined") {
        if ( document.addEventListener ) {
            document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
        } else if (document.attachEvent) {
            document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
            document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
        }
    } else {
        onBridgeReady();
    }
</script>
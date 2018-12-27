<?php
namespace EasyPay;

class Payment
{
    // 微信扫码支付
    const WX_QR_PAY = 'wechat.qr.pay';

    // 微信公众号支付
    const WX_PUB_PAY = 'wechat.pub.pay';

    // 微信公众号支付
    const WX_PROGRAM_PAY = 'wechat.program.pay';

    // 微信企业转账
    const WX_TRANSFER = 'wechat.transfer';

    // 支付宝扫码支付
    const ALI_QR_PAY = 'ali.qr.pay';

    // 支付宝网页支付
    const ALI_WAP_PAY = 'ali.wap.pay';

    // 支付宝企业转账
    const ALI_TRANSFER = 'ali.transfer';
}
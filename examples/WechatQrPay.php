<?php
include "bootstrap.php";

use EasyPay\Payment;
use EasyPay\PayFactory;

try {
    // 使用微信扫码支付
    $trade = PayFactory::create(Payment::WX_QR_PAY);

    // EasyPay生成的支付跳转url
    $url = $trade->execute([
        // 支付信息
        'attach'            => 'wechat pay test',
        // 支付订单信息
        'body'              =>  '微信扫码支付,测试订单',
        // 支付订单号
        'out_trade_no'      => substr(md5(uniqid()), 0, 18) . date("YmdHis"),
        // 支付金额,单位为分
        'total_amount'      =>  '1',
        // 客户端IP
        'spbill_create_ip'  => $_SERVER['REMOTE_ADDR'],
        // 产品号
        'product_id'        =>  '123'
    ]);

    // 生成二维码
    $qrCode = new \Endroid\QrCode\QrCode($url);
    $qrCode->setSize(300);
    $qrCode->setWriterByName('png');

    header('Content-Type: ' . $qrCode->getContentType());
    echo $qrCode->writeString();
} catch (\Exception $e) {
    // 打印错误县信息
    echo "错误信息为 : {$e->getMessage()}","<br>";
    echo "错误文件为 : {$e->getFile()}, 错误所在行 : {$e->getLine()}";
}
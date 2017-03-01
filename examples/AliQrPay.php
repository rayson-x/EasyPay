<?php
include "bootstrap.php";

use EasyPay\Trade;
use EasyPay\Payment;

try {
    $trade = new Trade(Payment::ALI_QR_PAY);

    $url = $trade->execute([
        // 订单标题
        'subject'           =>  "ali pay test",
        // 订单详细信息
        'body'              =>  "这是支付宝支付的测试订单",
        // 订单号(随机生成的订单号,最长为64位)
        'out_trade_no'      =>  substr(md5(uniqid()),0,18).date("YmdHis"),
        // 支付金额,单位为元,最小可精确到分(0.01)
        'total_amount'      =>  '1',
        // 商品类型 0—虚拟类商品，1—实物类商品
        'goods_type'        =>  '1',
        // 订单超时时间(m-分钟，h-小时，d-天，1c-当天)
        'timeout_express'   =>  '15m',
    ]);

    // 生成二维码
    $qrCode = (new Endroid\QrCode\QrCode($url))->setSize(300);
    header('Content-Type: image/png');
    echo $qrCode->get('png');
} catch (\Exception $e) {
    // 打印错误县信息
    echo "错误信息为 : {$e->getMessage()}","<br>";
    echo "错误文件为 : {$e->getFile()}, 错误行为 : {$e->getLine()}";
}
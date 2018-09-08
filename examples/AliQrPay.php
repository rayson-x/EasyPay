<?php
include "bootstrap.php";

use EasyPay\Payment;
use EasyPay\PayFactory;

try {
    // 支付宝扫码支付
    $trade = PayFactory::create(Payment::ALI_QR_PAY);

    $url = $trade->execute([
        // 订单标题
        'subject'               =>  "ali pay test",
        // 订单详细信息
        'body'                  =>  "这是支付宝支付的测试订单",
        // 订单号(随机生成的订单号,最长为64位)
        'out_trade_no'          =>  substr(md5(uniqid()),0,18).date("YmdHis"),
        // 支付金额,单位为元,最小可精确到分(0.01)
        'total_amount'          =>  '1',
        // 商品类型 0—虚拟类商品，1—实物类商品
        'goods_type'            =>  '1',
        // 订单超时时间(m-分钟，h-小时，d-天，1c-当天)
        'timeout_express'       =>  '15m',
        // 支付完成后,异步通知地址
        'notify_url'            =>  'http://examples.com/',
        // 收款支付宝用户ID
        'seller_id'             =>  '',
        // 可打折金额. 参与优惠计算的金额，单位为元
        'discountable_amount'   =>  '',
        // 不可打折金额. 不参与优惠计算的金额，单位为元
        'undiscountable_amount' =>  '',
        // 买家支付宝账号
        'buyer_logon_id'        =>  '',
        // 业务扩展参数(详细参数参考支付宝文档)
        'extend_params'         =>  '',
        // 描述分账信息(详细参数参考支付宝文档)
        'royalty_info'          =>  '',
        // 二级商户信息(详细参数参考支付宝文档)
        'sub_merchant'          =>  '',
        // 支付宝店铺的门店ID
        'alipay_store_id'       =>  '',
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
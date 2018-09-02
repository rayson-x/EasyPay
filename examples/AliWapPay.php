<?php
include "bootstrap.php";

use EasyPay\Payment;
use EasyPay\PayFactory;

try {
    // 使用支付宝网页支付
    $trade = PayFactory::create(Payment::ALI_WAP_PAY);
    // EasyPay生成的支付跳转url
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
        // 支付完后,用户返回的页面
        'return_url'            =>  'http://examples.com/',
        // 收款支付宝用户ID
        'seller_id'             =>  '',
        // 用户授权码
        'auth_token'            =>  '',
        // 销售产品码,默认为 QUICK_WAP_PAY
        'product_code'          =>  'QUICK_WAP_PAY',
        // 公用回传参数
        'passback_params'       =>  '',
        // 优惠参数
        'promo_params'          =>  '',
        // 业务扩展参数(详细请查看接口文档)
        'extend_params'         =>  '',
        // 指定用户支付渠道,通过","进行分隔
        'enable_pay_channels'   =>  '',
        // 指定用户不可用的渠道,通过","进行分隔
        'disable_pay_channels'  =>  '',
        // 商户门店编号
        'store_id'              =>  '',
    ]);

    // 支付宝支付方式为生成收银台url,然后跳转,由用户进行支付
    header("Location: {$url}");
} catch (\Exception $e) {
    // 打印错误县信息
    echo "错误信息为 : {$e->getMessage()}","<br>";
    echo "错误文件为 : {$e->getFile()}, 错误所在行 : {$e->getLine()}";
}
<?php
include "bootstrap.php";

use EasyPay\Payment;
use EasyPay\PayFactory;

try {
    // 使用阿里企业转账
    $trade = PayFactory::create(Payment::ALI_TRANSFERS);

    // 进行企业转账
    $data = $trade->execute([
        // 转账订单号
        'out_biz_no'            =>  substr(md5(uniqid()),0,18).date("YmdHis"),
        // 转账用户类型,分别为用户唯一ID与用户登录账户
        'payee_type'            =>  'ALIPAY_LOGONID',
        // 与 payee_type 选项进行联动
        'payee_account'         =>  'vcsrag0954@sandbox.com',
        // 转账金额,单位为元,精确到分
        'amount'                =>  '1',
        // 付款方真实姓名
        'payer_real_name'       =>  '',
        // 付款方显示姓名
        'payer_show_name'       =>  '',
        // 收款方真实姓名
        'payee_real_name'       =>  '',
        // 转账备注（支持200个英文/100个汉字）
        'remark'                =>  '',
        // 扩展参数(详细参数参考支付宝文档)
        'ext_param'             =>  '',
    ]);

    // 支付宝服务器响应结果
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
} catch (\Exception $e) {
    // 打印错误县信息
    echo "错误信息为 : {$e->getMessage()}","<br>";
    echo "错误文件为 : {$e->getFile()}, 错误所在行 : {$e->getLine()}";
}
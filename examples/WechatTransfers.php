<?php
include "bootstrap.php";

use EasyPay\Payment;
use EasyPay\PayFactory;

try {
    // 使用微信企业转账
    $trade = PayFactory::create(Payment::WX_TRANSFERS);
    // 进行企业转账
    $data = $trade->execute([
        'partner_trade_no'  =>  substr(md5(uniqid()), 0, 18) . date("YmdHis"),
        'openid'            =>  'okUzQw52RfmBwO4H1d8M-bHPo8Vw',
        'check_name'        =>  'NO_CHECK',
        'amount'            =>  '1',
        'desc'              =>  '测试',
        'spbill_create_ip'  =>  '127.0.0.1',
    ]);

    // 微信服务器响应结果
    echo "<pre>";
    var_dump($data);
    echo "</pre>";
} catch (\Exception $e) {
    // 打印错误县信息
    echo "错误信息为 : {$e->getMessage()}","<br>";
    echo "错误文件为 : {$e->getFile()}, 错误所在行 : {$e->getLine()}";
}
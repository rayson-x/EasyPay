<?php
include "../vendor/autoload.php";

try {
    // 使用微信扫码支付
    $trade = new \EasyPay\Trade('wechat.qr.pay' , [
    ]);

    $url = $trade->execute([
    ]);

} catch (\Exception $e) {
    // 打印错误县信息
    echo "错误信息为 : {$e->getMessage()}","<br>";
    echo "错误文件为 : {$e->getFile()}, 错误行为 : {$e->getLine()}";
}
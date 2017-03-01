## 简单使用

```php
require "vendor/autoload.php";

try {
    // 使用支付宝网页支付
    $trade = new \EasyPay\Trade('ali.wap.pay', [
        'app_id'            =>  '2016073100130857',
        'ssl_private_key'   =>  'ssl/ali/rsa1/rsa_private_key.pem',
        'ali_public_key'    =>  'ssl/ali/rsa1/ali_public_key.pem',
        'is_sand_box'       =>  true,
    ]);

    // EasyPay生成的支付跳转url
    $url = $trade->execute([
        // 支付宝参数
        'body'              =>  "亦可赛艇",
        'subject'           =>  "续一秒",
        'out_trade_no'      =>  substr(md5(uniqid()),0,18).date("YmdHis"),
        'total_amount'      =>  '1',
        'goods_type'        =>  '1',
        'timeout_express'   =>  '15m',
        'return_url'        =>  'http://192.168.0.106/notify.php'
    ]);

    // 跳转到支付页面
    header("Location: {$url}");
} catch (\Exception $e) {
    var_dump($e->getMessage());
    var_dump($e->getLine(), $e->getFile());
}
```

## Todo
* 完善支付宝其他功能
* 将RSA加密分离为单独的库,同时添加密钥解析
* 支持多种编码(目前仅支持utf-8)
* 文档待补充
* 将支付宝参数构造功能进行抽象

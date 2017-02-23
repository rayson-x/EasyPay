## 简单使用

```
namespace EasyPay;

require "vendor/autoload.php";

Config::loadConfig([
    'wechat' => [
        // 绑定支付的APPID
        'appid'         => 'xxxxxxxxxxxxxxxxxx',
        // 商户支付密钥
        'key'           => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        // 商户号
        'mch_id'        => 'xxxxxxxxxx',
        // 异步通知地址
        'notify_url'    => 'http://foobar.com',
        // ssl证书路径 (退款必须使用ssl)
        'ssl_cert_path' => __DIR__.DIRECTORY_SEPARATOR.'ssl/apiclient_cert.pem',
        // ssl密钥路径 (退款必须使用ssl)
        'ssl_key_path'  => __DIR__.DIRECTORY_SEPARATOR.'ssl/apiclient_key.pem'
    ],
    'ali'   =>  [
        // app_id
        'app_id'            => '2016072900120125',
        // RSA签名所用的私钥证书
        'ssl_private_key'   =>  'ssl/ali/rsa1/rsa_private_key_pkcs8.pem',
        // 是否是沙箱测试
        'is_sand_box'       =>  true,
    ]
]);

try {
    // 使用支付宝网页支付
    $trade = new Trade(Trade::ALI_WAP_PAY);

    // EasyPay生成的支付跳转url
    $url = $trade->execute([
        // 支付宝参数
        'body'              =>  "亦可赛艇",
        'subject'           =>  "续一秒",
        'out_trade_no'      =>  substr(md5(uniqid()),0,18).date("YmdHis"),
        'total_amount'      =>  '90000',
        'goods_type'        =>  '1',
        'timeout_express'   =>  '15m',
    ]);

    // 跳转到支付页面
    header("Location: {$url}");
} catch (\Exception $e) {
    var_dump($e->getMessage());
    var_dump($e->getLine(),$e->getFile());
}
```

## Todo
* 完善支付宝其他功能
* 将RSA加密分离为单独的库,同时添加密钥解析
* 加载支付配置时,将配置信息与订单信息分离
* 用更加优雅的方式载入支付方式
* 文档待补充

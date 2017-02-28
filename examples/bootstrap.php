<?php
include "../vendor/autoload.php";

\EasyPay\Config::loadConfig([
    'wechat'    =>  [
        // 应用id
        'appid'         =>  'xxxxxxxxxxxxxxxxxx',
        // 应用密钥
        'key'           =>  'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        // 商户ID
        'mch_id'        =>  'xxxxxxxxxx',
        // 回调地址
        'notify_url'    => 'http://example.com',
    ],
    'ali'       =>  [
        // 支付宝应用id
        'app_id'            =>  '2016072900120125',
        // 签名加密方式(目前仅支持RSA,RSA2两种)
        'sign_type'         =>  'RSA',
        // 生成的RSA密钥,用于生成签名(可用openssl或者支付宝自带的密钥生成器来生成)
        'ssl_private_key'   =>  'ssl/ali/rsa1/rsa_private_key.pem',
        // 支付宝提供的公钥,用于验证签名
        'ali_public_key'    =>  'ssl/ali/rsa1/ali_public_key.pem',
        // 是否是沙箱测试(默认为沙箱测试)
        'is_sand_box'       =>  true
    ]
]);
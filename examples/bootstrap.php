<?php
include __DIR__ . "/../vendor/autoload.php";

// WEIXIN_APPID=
// WEIXIN_APPSECRET=
// WEIXIN_MCH_ID=
// WEIXIN_MCH_KEY=

foreach (explode("\n", file_get_contents(__DIR__ . "/../.env")) as $env) {
    if (false === strpos($env, '=')) {
        continue;
    }

    putenv($env);
}

\EasyPay\Config::loadConfig([
    'wechat'    =>  [
        // 应用id
        'appid'             =>  getenv('WEIXIN_APPID'),
        // 应用密钥
        'key'               =>  getenv('WEIXIN_MCH_KEY'),
        // 商户ID
        'mch_id'            =>  getenv('WEIXIN_MCH_ID'),
        // 回调地址
        'notify_url'        =>  'http://example.com',
        // ssl证书路径
        'ssl_cert_path'     =>  getenv('WEIXIN_CERT_FILE'),
        // ssl密钥路径
        'ssl_key_path'      =>  getenv('WEIXIN_KEY_FILE'),
    ],
    'ali'       =>  [
        // 支付宝应用id
        'app_id'            =>  '2016072900120125',
        // 签名加密方式(目前仅支持RSA,RSA2两种)
        'sign_type'         =>  'RSA2',
        // 生成的RSA密钥,用于生成签名(可用openssl或者支付宝自带的密钥生成器来生成)
        'ssl_private_key'   =>  __DIR__ . '/ssl/ali/rsa2/rsa_private_key.pem',
        // 支付宝提供的公钥,用于验证签名
        'ali_public_key'    =>  __DIR__ . '/ssl/ali/rsa2/ali_public_key.pem',
        // 是否是沙箱测试(默认为沙箱测试)
        'is_sand_box'       =>  true,
    ]
]);
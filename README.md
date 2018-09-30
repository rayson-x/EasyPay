## 简单使用

```php
require "vendor/autoload.php";

use EasyPay\Payment;
use EasyPay\PayFactory;

// 使用支付宝网页支付
$trade = PayFactory::create(Payment::ALI_WAP_PAY, [
    // 应用ID
    'app_id'            =>  '2016073100130857',
    // 用户生成的私钥证书
    'ssl_private_key'   =>  'ssl/ali/rsa1/rsa_private_key.pem',
    // 阿里提供的公钥证书
    'ali_public_key'    =>  'ssl/ali/rsa1/ali_public_key.pem',
    // 沙箱测试
    'is_sand_box'       =>  true,
]);

// EasyPay生成的支付跳转url
$url = $trade->execute([
    // 订单标题
    'subject'               =>  "ali pay test",
    // 订单详细信息
    'body'                  =>  "这是支付宝支付的测试订单",
    // 订单号(随机生成的订单号,最长为64位)
    'out_trade_no'          =>  substr(md5(uniqid()), 0, 18) . date("YmdHis"),
    // 支付金额,单位为分
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
    // 销售产品码
    'product_code'          =>  '',
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
```

```php
use EasyPay\Payment;
use EasyPay\PayFactory;

// 使用微信扫码支付
$trade = PayFactory::create(Payment::WX_QR_PAY, [
    // 应用id
    'appid'             =>  '',
    // 应用密钥
    'key'               =>  '',
    // 商户ID
    'mch_id'            =>  '',
    // 回调地址
    'notify_url'        =>  '',
    // ssl证书路径
    'ssl_cert_path'     =>  '',
    // ssl密钥路径
    'ssl_key_path'      =>  '',
]);

// 支付信息
$trade->attach           = 'wechat pay test';
// 支付订单信息
$trade->body             = '微信扫码支付,测试订单';
// 支付订单号
$trade->out_trade_no     = substr(md5(uniqid()), 0, 18) . date("YmdHis");
// 支付金额,单位为分
$trade->total_fee        = '1';
// 客户端IP
$trade->spbill_create_ip = $_SERVER['REMOTE_ADDR'];
// 商品id
$trade->product_id       = '123';

// EasyPay生成的支付跳转url
$url = $trade->execute();

// 生成二维码
$qrCode = (new Endroid\QrCode\QrCode($url))->setSize(300);
header('Content-Type: image/png');
echo $qrCode->get('png');
```

## 获取通知信息
```php
use EasyPay\Notify;

// 获取微信通知信息
$notify = Notify::get('wechat');
// 获取支付宝通知信息
$notify = Notify::get('ali');
```

可以通过laravel,symfony,psr-7的request对象来构建通知消息对象
> $notify = Notify::get(string $service [, Symfony\Component\HttpFoundation\Request | Illuminate\Http\Request | Psr\Http\Message\RequestInterface $request = null]);

## Todo
* 将RSA加密分离为单独的库,同时添加密钥解析
* 支持多种编码(目前仅支持utf-8)
* 文档待补充
* App支付
* Log功能
* 不在从全局获取配置信息,配置信息记录在当前交易的上下文中
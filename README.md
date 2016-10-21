一个简单的支付接口
=

### 这是一个十分简单的SDK现在已经完成了微信的支付，未来的目标是集成市面上所有的支付方式

## 发起订单
```php
require "vendor/autoload.php";

try{
    // 配置默认信息
    EasyPay\Config::loadConfig([
	    // 微信支付的参数
        'wechat' => [
            'appid' =>  'wxd678efh567hg6787',       // appid
            'mch_id'=>  '1230000109',               // 商户ID
            'notify_url' => 'http://127.0.0.1',     // 回调地址
        ]
    ]);

    // 发送订单到第三方服务器，$result为第三方返回的数据
    // EasyPay用于交互的数据都为实现了ArrayAccess与ArrayIterator接口的对象
	// 所以$result可以作为对象使用,也可以作为数组使用,不过不支持array系列函数的使用
    $result = (new EasyPay\EasyPay)
        // 订单信息,具体字段可以参考支付的官网
        ->ready([
            'attach' => 'wechat pay test',
            'body'  =>  '订单详细',
            'out_trade_no' => substr(md5(uniqid()),0,18).date("YmdHis"),
            'total_fee' => 1,
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'trade_type' => 'NATIVE',
            'product_id' => '12235413214070356458058'
        ])
        // 使用的支付方式
        ->sendTo('wechat')
        // 请求的接口(详细接口见附录的默认API)
        ->initOrder();

	// 此处实现业务逻辑

}catch(\EasyPay\Exception\PayException $e){
    // 支付过程中出现错误会以异常的形式抛出
    // 使用者在此处对失败的订单进行处理
}
```

## 处理异步返回的信息
#### 1.`EasyPay`提供了十分方便的接口，可以让使用者不需要关心如何获取异步返回的数据，也不需要管响应第三方，只要安心处理业务逻辑就行了

```php
require "vendor/autoload.php";

// 在验证是一次成功的请求之后才会回调使用者提供的函数
(new \EasyPay\HandleNotify('wechat'))->handle(function(){
    // 在此处执行业务逻辑
    // 如果出现错误想中断，直接抛出异常，EasyPay捕获此异常并返回错误信息
});
```

## 附录

### 1.`EasyPay`内置了6个默认API，使用的时候只要改变链式操作的最后一步，就可以改变请求的接口
```php
//////////////////// 发起订单 ////////////////////
$result = (new EasyPay\EasyPay)
        ->ready($order)
        ->sendTo(EasyPay\EasyPay::WX)
        ->initOrder();

//////////////////// 查询订单 ////////////////////
$result = (new EasyPay\EasyPay)
        ->ready($order)
        ->sendTo(EasyPay\EasyPay::WX)
        ->orderQuery();

//////////////////// 关闭订单 ////////////////////
$result = (new EasyPay\EasyPay)
        ->ready($order)
        ->sendTo(EasyPay\EasyPay::WX)
        ->closeOrder();
```
#### 具体可用接口
```php
interface PayApiInterface
{
    /**
     * 发起支付订单,返回第三方提供的支付数据
     *
     * @return array|object
     */
    public function initOrder();

    /**
     * 查询订单信息,返回订单信息
     *
     * @return array|object
     */
    public function orderQuery();

    /**
     * 关闭订单
     *
     * @return array|object
     */
    public function closeOrder();

    /**
     * 申请退款
     *
     * @return array|object
     */
    public function refund();

    /**
     * 查询退款信息
     *
     * @return array|object
     */
    public function refundQuery();

    /**
     * 下载账单
     *
     * @return array|object
     */
    public function downloadBill();
}
```

### 2.异常

* PayException `PayFailException`跟`SignVerifyFailException`基类，提供了`getResult`方法获取数据集，父类是`RuntimeException`
* PayFailException 响应信息验证失败时抛出，提供了`getErrCode`方法获取错误码(错误码可以为字符串)
* SignVerifyFailException 签名验证失败时抛出
* PayParamException 当必要参数缺少时抛出，父类是`InvalidArgumentException`


### 3.获取配置信息
```php
require "vendor/autoload.php";

// 加载默认设置
EasyPay\Config::loadConfig([
    'wechat' => [
        'appid' =>  'wxd678efh567hg6787',
        'mch_id'=>  '1230000109',
        'notify_url' => 'host',
    ],
    'alipay' => [
        'appid' =>  'ala7zegsdg67hga8q73',
        'method'=>  'alipay.trade.wap.pay',
        'notify_url' => 'host',
    ]
]);

// 获取微信的appid，返回值为“wxd678efh567hg6787”
EasyPay\Config::wechat('appid');

// 获取微信的APPID跟商户号,返回一个数组['appid' => 'wxd678efh567hg6787','mch_id' => '1230000109']
EasyPay\Config::wechat('appid','mch_id');

// 获取支付宝所有的配置信息，返回支付宝所有配置信息
EasyPay\Config::alipay();
```

### 4.Todo
* 实现一个简单的HTTP请求库
* 加入其它支付方式
* 提高代码质量
* 通过枚举的方式选择支付方式
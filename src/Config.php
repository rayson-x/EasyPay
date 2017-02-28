<?php
namespace EasyPay;

/**
 * Class Config
 * @package EasyPay
 *
 * @method static wechat(...$key)
 * @method static ali(...$key)
 */
class Config
{
    /**
     * 配置信息
     *
     * @var array
     */
    protected static $config = [
        // 微信配置信息
        'wechat' => [
            // 应用id
            'appid'         => '',
            // 商户支付密钥
            'key'           => '',
            // 商户号
            'mch_id'        => '',
            // 签名加密方式
            'sign_type'     => 'MD5',
            // 异步通知地址
            'notify_url'    => '',
            // ssl证书路径
            'ssl_cert_path' => '',
            // ssl密钥路径
            'ssl_key_path'  => '',
        ],
        // 支付宝配置信息
        'ali' => [
            // 应用id
            'app_id'            =>  '2016072900120125',
            // 沙箱测试开关
            'is_sand_box'       =>  true,
            // 生成的RSA证书私钥,用于生成签名
            'ssl_private_key'   =>  '',
            // 阿里提供的公钥,用于验证签名
            'ali_public_key'    =>  '',
            // 格式
            'format'            =>  'JSON',
            // 字符编码
            'charset'           =>  'UTF-8',
            // 签名加密方式
            'sign_type'         =>  'RSA',
            // 支付宝api版本
            'version'           =>  '1.0',
            // 销售产品码
            'product_code'      =>  'QUICK_WAP_PAY'
        ],
    ];

    /**
     * 注册配置信息
     *
     * @param array $config
     */
    public static function loadConfig(array $config)
    {
        foreach ($config as $key => $option) {
            static::$config[$key] = isset(static::$config[$key])
                ? array_merge(static::$config[$key], $option)
                : $option;
        }
    }

    /**
     * 获取配置信息
     *
     * @param $method
     * @param $key
     * @return null
     */
    public static function getConfig($method, $key)
    {
        return isset(static::$config[$method][$key])
            ? static::$config[$method][$key]
            : null;
    }

    /**
     * @param $method
     * @param $key
     * @param $value
     */
    public static function setConfig($method, $key, $value)
    {
        static::$config[$method][$key] = $value;
    }

    /**
     * @param $method
     * @param $key
     * @return bool
     */
    public static function exists($method, $key)
    {
        return isset(static::$config[$method][$key]);
    }

    /**
     * 通过重载的方式获取配置信息
     *
     * @param $method
     * @param $args
     * @return array|null|string
     */
    public static function __callStatic($method, $args)
    {
        $result = [];

        switch (count($args)) {
            case 0:
                $result = static::$config[$method];
                break;
            case 1:
                $result = static::getConfig($method, ...$args);
                break;
            default:
                foreach ($args as $arg) {
                    $result[$arg] = static::getConfig($method, $arg);
                }
                break;
        }

        return $result;
    }
}

<?php
namespace EasyPay;


class Config
{
    // 配置信息
    protected static $config = [
        // 微信配置信息
        'wechat' => [
            'appid'         => '',                                              // 绑定支付的APPID
            'key'           => '',                                              // 商户支付密钥
            'mch_id'        => '',                                              // 商户号
            'notify_url'    => '',                                              // 异步通知地址
            'sslcert_path'  => '',                                              // ssl证书路径
            'sslkey_path'   => '',                                              // ssl密钥路径
        ],
        // 支付宝配置信息
        'alipay' => [

        ],
    ];

    /**
     * 注册配置信息
     *
     * @param array $config
     */
    public static function loadConfig(array $config)
    {
        foreach($config as $key => $option){
            static::$config[$key] = isset(static::$config[$key])
                ? array_merge(static::$config[$key],$option)
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
    public static function getConfig($method,$key)
    {
        return isset(static::$config[$method][$key])
            ? static::$config[$method][$key]
            : null;
    }

    /**
     * 通过重载的方式获取配置信息
     *
     * @param $method
     * @param $args
     * @return array|null|string
     */
    public static function __callStatic($method,$args)
    {
        $result = [];

        switch(count($args)){
            case 0:
                $result = static::$config[$method];
                break;
            case 1:
                $result = static::getConfig($method,array_shift($args));
                break;
            default:
                foreach($args as $arg){
                    $result[$arg] = static::getConfig($method,$arg);
                }
                break;
        }

        return $result;
    }
}

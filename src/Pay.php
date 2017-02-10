<?php
namespace EasyPay;

class Pay
{
    protected $data;

    protected $api = [
        'wechat' => \EasyPay\PayApi\Wechat\PayApi::class,
        'alipay' => \EasyPay\PayApi\Alipay\PayApi::class,
    ];

    /**
     * @param array $data
     * @return $this
     */
    public static function ready(array $data)
    {
        return (new static)->setConfig($data);
    }

    /**
     * ÉèÖÃÅäÖÃÎÄ¼ş
     *
     * @param array $data
     * @return $this
     */
    public function setConfig(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param $destination
     * @return \EasyPay\Interfaces\PayApiInterface
     */
    public function sendTo($destination)
    {
        $instance = $this->getInstance($destination);

        if(!$instance instanceof \EasyPay\Interfaces\PayApiInterface) {
            throw new \RuntimeException;
        }

        return $instance;
    }

    /**
     * @param $name
     * @return \EasyPay\Interfaces\PayApiInterface
     */
    public function getInstance($name)
    {
        $class = isset($this->api[$name]) ? $this->api[$name] : $name;

        return (new $class($this->data));
    }
}
<?php
namespace EasyPay\Utils;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

/**
 * 简易的Http客户端
 *
 * Class HttpClient
 * @package EasyPay\Utils
 */
class HttpClient
{
    protected $method = null;

    protected $uri = null;

    protected $options = [
        CURLOPT_TIMEOUT         =>  30,
        CURLOPT_ENCODING        =>  '',
        CURLOPT_IPRESOLVE       =>  1,
        CURLOPT_RETURNTRANSFER  =>  true,
        CURLOPT_SSL_VERIFYPEER  =>  false,
        CURLOPT_SSL_VERIFYHOST  =>  false,
        CURLOPT_CONNECTTIMEOUT  =>  10,
        CURLOPT_HEADER          =>  true
    ];

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     */
    public function __construct($method, $uri, array $options = [])
    {
        $this->method  = $method;
        $this->uri     = $uri;
        $this->options = $options;
    }

    /**
     * @param $item
     * @param null $value
     * @return $this
     */
    public function setCurlOption($item, $value = null)
    {
        if (is_array($item)) {
            foreach($item as $key => &$value){
                $this->options[$key] = $value;
            }
        } else {
            $this->options[$item] = $value;
        }

        return $this;
    }

    /**
     * @param $item
     * @param null $default
     * @return null
     */
    public function getCurlOption($item, $default = null)
    {
        return array_key_exists($item, $this->options)
            ? $this->options[$item]
            : $default;
    }

    /**
     * 发送指定内容到服务端
     *
     * @param null|string $body
     * @return ResponseInterface
     */
    public function send($body = null)
    {
        $client = new Client;

        return $client->{$this->method}($this->uri, [
            'body'  =>  $body,
            'curl'  =>  $this->options
        ]);
    }
}
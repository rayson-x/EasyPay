<?php

namespace EasyPay\TradeData;

use ArrayAccess;
use ArrayIterator;
use JsonSerializable;
use IteratorAggregate;
use EasyPay\Utils\XmlElement;
use UnexpectedValueException;
use EasyPay\Exception\PayParamException;

/**
 * Class BaseTradeData
 * @package EasyPay\TradeData
 */
abstract class BaseTradeData implements ArrayAccess, JsonSerializable, IteratorAggregate
{
    /**
     * 生成的数据
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * 原始数据
     *
     * @var array
     */
    protected $original = [];

    /**
     * 支付配置项
     *
     * @var array
     */
    protected $options = [];

    /**
     * 通过XML获取数据集
     *
     * @param string[XML] $input
     * @return static
     */
    public static function createFromXML($input, $options = [])
    {
        $backup = libxml_disable_entity_loader(true);
        $result = simplexml_load_string($input, XmlElement::class, LIBXML_NOCDATA);
        libxml_disable_entity_loader($backup);

        if ($result === false) {
            throw new UnexpectedValueException('XML Error');
        }

        return new static($result->toArray(), $options);
    }

    /**
     * 通过JSON数据获取数据集
     *
     * @param $input
     * @param $depth
     * @param $options
     * @return static
     */
    public static function createFromJson($input, $options = [])
    {
        $result = json_decode($input, true);

        if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new UnexpectedValueException(
                json_last_error_msg(),
                json_last_error()
            );
        }

        return new static($result, $options);
    }

    /**
     * BaseTradeData Construct
     *
     * @param \Iterator|array $attributes
     * @param array $options
     */
    public function __construct($attributes, $options = [])
    {
        $this->replace($attributes);

        $this->original = $this->attributes;
        $this->options  = $options;
    }

    /**
     * 替换原有数据
     *
     * @param \Iterator|array $attributes
     */
    public function replace($attributes)
    {
        $this->attributes = [];

        $this->setAttributes($attributes);
    }

    /**
     * 设置数据
     *
     * @param \Iterator|array $attributes
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * @param $attribute
     * @param $value
     */
    public function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }

    /**
     * @param $attribute
     * @return mixed
     */
    public function getAttribute($attribute)
    {
        return $this->offsetExists($attribute) ? $this->attributes[$attribute] : null;
    }

    /**
     * 获取源数据
     *
     * @param $key
     * @return mixed
     */
    public function getOriginal($key = null)
    {
        if (is_null($key)) {
            return $this->original;
        }

        if (!array_key_exists($key, $this->original)) {
            return null;
        }

        return $this->original[$key];
    }

    /**
     * 转换为数组格式
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getOption($key)
    {
        return array_key_exists($key, $this->options) ? $this->options[$key] : null;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setOption($key, $value)
    {
        return $this->options[$key] = $value;
    }

    /**
     * 生成JSON数据
     *
     * @return string
     */
    public function toJson()
    {
        $value = json_encode($this);

        if ($value === false && json_last_error() !== JSON_ERROR_NONE) {
            throw new UnexpectedValueException(json_last_error_msg(), json_last_error());
        }

        return $value;
    }

    /**
     * 生成XML数据
     *
     * @return string
     */
    public function toXml()
    {
        $dom = new XmlElement('<xml/>');

        foreach ($this as $key => $value) {
            $dom->addChild($key, $value);
        }

        return $dom->asXML();
    }

    /**
     * 检查必要参数是否存在,且不为空
     *
     * @param array $params
     */
    public function checkParamsEmpty(array $params)
    {
        foreach ($params as $param) {
            if (empty($this->$param)) {
                throw new PayParamException("{$param}不存在或者为空,请检查配置信息");
            }
        }
    }

    /**
     * 选中指定参数
     *
     * @param array $params
     */
    public function selected(array $params)
    {
        $attributes = [];
        foreach ($params as $name) {
            if ($this->offsetExists($name)) {
                $attributes[$name] = $this[$name];
            }
        }

        $this->replace($attributes);
    }

    /**
     * 产生随机字符串
     *
     * @param int $length
     * @return string
     */
    public function createNonceStr($length = 32)
    {
        static $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';

        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }

        return $str;
    }

    /**
     * 设置签名
     *
     * @param null $sign
     * @return void
     */
    public function setSign($sign = null)
    {
        $this['sign'] = (string) $sign ?: $this->makeSign();
    }

    /**
     * 生成签名
     *
     * @return string
     */
    abstract public function makeSign();

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * @param mixed $offset
     * @return null
     */
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->attributes);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * 检查参数是否为空
     * PHP7后isset有改动,如果用了重载的方式加载对象属性,可能会出现错误
     *
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        $this->offsetUnset($name);
    }
}

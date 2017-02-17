<?php
namespace EasyPay\Utils;

/**
 * 提供数组式的使用方法
 *
 * Class XmlElement
 * @package PayApi
 */
class XmlElement extends \SimpleXMLIterator implements \ArrayAccess
{
    public function offsetSet($offset,$value)
    {
        $this->$offset = $value;
    }

    public function offsetGet($offset)
    {
        return isset($this->$offset) ? $this->$offset : null;
    }

    public function offsetExists($offset)
    {
        return property_exists($this,$offset);
    }

    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}
<?php 

namespace EasyPay\Notifies;

use EasyPay\TradeData\BaseTradeData;
use EasyPay\Interfaces\NotifyInterface;

/**
 * 支付通知信息
 *
 * @method mixed getOriginal(string $name = null)
 * @method array toArray()
 * @method string toJson()
 * @method string toXml()
 * 
 * Class BaseNoitfy
 * @package EasyPay\Notifies
 */
abstract class BaseNoitfy implements NotifyInterface
{
    /**
     * @var BaseTradeData
     */
    protected $tradeData;

    /**
     * @var array
     */
    protected static $badModes = ['cli', 'continuity', 'milter', 'webjames'];

    /**
     * @param mixed $offset
     * @return null
     */
    public function offsetGet($offset)
    {
        return $this->tradeData->offsetGet($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->tradeData->offsetSet($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->tradeData->offsetUnset($offset);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->tradeData->offsetExists($offset);
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name,$value)
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
     * @param $method
     * @param array $params
     * @return mixed
     */
    public function __call($method, $params)
    {
        return $this->tradeData->$method(...$params);
    }

    abstract protected function verifySign();
}
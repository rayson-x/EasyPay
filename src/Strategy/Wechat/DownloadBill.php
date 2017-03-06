<?php
namespace EasyPay\Strategy\Wechat;
use Ant\Support\Arr;

/**
 * 下载账单
 *
 * Class DownloadBill
 * @package EasyPay\Strategy\Wechat\Transaction
 */
class DownloadBill extends BaseWechatStrategy
{
    // Todo 以流的形式进去读取

    /**
     * {@inheritDoc}
     */
    protected function getRequireParams()
    {
        return ['appid','mch_id','bill_date','bill_type'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFillParams()
    {
        return ['appid','mch_id','bill_date','bill_type'];
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequestMethod()
    {
        return 'POST';
    }

    /**
     * {@inheritDoc}
     */
    protected function getRequestTarget()
    {
        return BaseWechatStrategy::DOWN_LOAD_BILL_URL;
    }

    /**
     * {@inheritDoc}
     */
    protected function handleData($result)
    {
        // todo 保存为文件
        return $result;
    }
}
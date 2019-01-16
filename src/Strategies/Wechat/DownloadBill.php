<?php

namespace EasyPay\Strategies\Wechat;

use EasyPay\TradeData\Ali\TradeData;
use EasyPay\Exception\PayFailException;

/**
 * 下载账单
 *
 * Class DownloadBill
 * @package EasyPay\Strategies\Wechat\Transaction
 */
class DownloadBill extends BaseWechatStrategy
{
    protected $savePath;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        if (array_key_exists('save_path', $options)) {
            $this->savePath = $options['save_path'];
        }

        parent::__construct($options);
    }

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
}
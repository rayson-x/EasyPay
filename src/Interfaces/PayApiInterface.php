<?php
namespace EasyPay\Interfaces;


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